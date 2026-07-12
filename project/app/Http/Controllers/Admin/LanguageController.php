<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

class LanguageController extends Controller
{

    public function bulkUpdate(Request $request, $code)
    {
        goIfUserCan('save-language');

        $language = Language::where('code', $code)->firstOrFail();

        $translations = $request->input('translations', []);

        $filePath = lang_path($code . '.json');

        $existing = [];
        if (File::exists($filePath)) {
            $existing = json_decode(File::get($filePath), true);
        }

        foreach ($translations as $key => $value) {
            $existing[$key] = $value;
        }

        File::put($filePath, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return redirect()
            ->back()
            ->with('success', __('Translations updated successfully.'));
    }

    public function addKeyword(Request $request, $code)
    {
        goIfUserCan('save-language');

        $request->validate([
            'keyword' => 'required|string',
            'value'   => 'required|string',
        ]);

        $language = Language::where('code', $code)->firstOrFail();

        $langPath = lang_path($code . '.json');

        $content = [];
        if (file_exists($langPath)) {
            $content = json_decode(file_get_contents($langPath), true) ?? [];
        }

        $content[$request->keyword] = $request->value;

        file_put_contents(
            $langPath,
            json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        return back()->withSuccess(__('Keyword added successfully to :lang', ['lang' => $language->name]));
    }

    public function translate($code)
    {
        goIfUserCan('view-language');

        $language = Language::where('code', $code)->firstOrFail();

        $title = __('Translate Language for ' . $language->name);

        $content = file_get_contents(lang_path($code . '.json'));
        $content = json_decode($content, true);

        $search = request()->query('search');

        $collection = collect($content)->filter(function ($value, $key) use ($search) {
            if (!$search) {
                return true; // no search, return all
            }

            return stripos($key, $search) !== false || stripos($value, $search) !== false;
        });

        $perPage = 15;
        $page = request()->get('page', 1);
        $items = $collection->slice(($page - 1) * $perPage, $perPage)->all();

        $paginator = new LengthAwarePaginator(
            $items,
            $collection->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.setting.language.translate', [
            'title' => $title,
            'language' => $language,
            'translations' => $paginator,
            'search' => $search,
        ]);
    }

    public function changeLang($lang)
    {
        goIfUserCan('save-language');

        session(['locale' => $lang]);

        return back()->withSuccess(__('Language changed successfully'));
    }

    private function transKeys()
    {
        $projectRoot = base_path();
        $translations = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($projectRoot, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $parser = (new ParserFactory())->createForNewestSupportedVersion();

        foreach ($iterator as $file) {
            if (strpos($file->getPathname(), '/vendor/') !== false) {
                continue;
            }

            // We will process both .php and .blade.php files with a combined approach
            if ($file->isFile() && in_array($file->getExtension(), ['php', 'blade.php'])) {
                $contents = file_get_contents($file->getPathname());

                // --- Regex pass for Blade files and simple PHP variables ---
                // Regex for __(), trans(), and @lang() with string literals
                preg_match_all(
                    "/(?:@lang|__|trans)\\(\\s*['\"]([^'\"]+)['\"]\\s*\\)/",
                    $contents,
                    $matches
                );
                if (!empty($matches[1])) {
                    $translations = array_merge($translations, $matches[1]);
                }

                // --- Attempt to resolve variables for your specific case ---
                // 1. Find all simple string variable assignments: $var = '...';
                preg_match_all("/\\\$([a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)\\s*=\\s*['\"]([^'\"]+)['\"]\\s*;/", $contents, $variableAssignments);

                $localVariables = [];
                if (!empty($variableAssignments[1])) {
                    foreach ($variableAssignments[1] as $index => $varName) {
                        // Store the last known value for a variable name
                        $localVariables[$varName] = $variableAssignments[2][$index];
                    }
                }

                // 2. Find all usages of __($var)
                preg_match_all("/(?:__|trans)\\(\\s*\\\$([a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)\\s*\\)/", $contents, $variableUsages);

                if (!empty($variableUsages[1])) {
                    foreach ($variableUsages[1] as $varName) {
                        // If we found an assignment for this variable, add its value
                        if (isset($localVariables[$varName])) {
                            $translations[] = $localVariables[$varName];
                        }
                    }
                }


                // --- AST pass for complex PHP files (but will skip variable usages) ---
                // We only run this on .php files as it will fail on Blade syntax
                if ($file->getExtension() === 'php') {
                    try {
                        $ast = $parser->parse($contents);
                        $traverser = new NodeTraverser();
                        $visitor = new class extends NodeVisitorAbstract
                        {
                            public $foundKeys = [];

                            public function enterNode(Node $node)
                            {
                                if ($node instanceof FuncCall && $node->name instanceof Node\Name) {
                                    if (in_array($node->name->toString(), ['__', 'trans'])) {
                                        // This part remains the same, it only finds string literals
                                        if (isset($node->getArgs()[0]) && $node->getArgs()[0]->value instanceof String_) {
                                            $this->foundKeys[] = $node->getArgs()[0]->value->value;
                                        }
                                    }
                                }
                            }
                        };
                        $traverser->addVisitor($visitor);
                        $traverser->traverse($ast);
                        $translations = array_merge($translations, $visitor->foundKeys);
                    } catch (Error $e) {
                        continue; // Skip files with parsing errors
                    }
                }
            }
        }

        return array_unique($translations);
    }

    public function keywords()
    {
        return response()->json([
            'status' => 'success',
            'keywords' => $this->transKeys()
        ]);
    }

    public function list()
    {
        $title = 'Languages';

        $languages = Language::latest()->paginate();

        return view('admin.setting.language.list', compact('title', 'languages'));
    }

    public function save(Request $request, $id = null)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:languages,code,' . $id,
        ]);

        $language       = $id ? Language::findOrFail($id) : new Language();
        $language->name = $request->name;
        $language->code = $request->code;
        $language->save();

        $langCode = $language->code;
        $langPath = lang_path("{$langCode}.json");

        if (!file_exists($langPath)) {
            file_put_contents($langPath, json_encode(new \stdClass(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        return back()->withSuccess(__('Language saved successfully'));
    }

    public function import(Request $request, $code)
    {
        $langCodes = Language::pluck('code')->prepend('system')->join(',');
        $request->validate([
            'code' => 'required|in:' . $langCodes
        ]);

        $langPath = lang_path($code . '.json');

        if (file_exists($langPath)) {
            $arrs = [];
            foreach ($this->transKeys() as $key) {
                $arrs[$key] = $key;
            };
            file_put_contents($langPath, json_encode($arrs));
        }

        return back()->withSuccess(__('Trans keys imported successfully'));
    }
}
