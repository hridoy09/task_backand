   @php
       
      $sections= app\Helpers\SystemHelper::sections();
    
   @endphp
   <ul class="manage-section-list">
        @forelse ($sections as $key => $section)
            <li class="manage-section-list__item {{ activeClass('admin.website.section.edit', $key) }}">
                <a href="{{ route('admin.website.section.edit', $key) }}" class="manage-section-list__link ">
                    <span class="manage-section-list__link-icon">
                     <i class="fas fa-cogs"></i>
                    </span>
                    <span class="manage-section-list__link-text">{{ $section['title'] ?? ucfirst($key) }}</span>
                </a>
            </li>
        @empty
            <li class="manage-section-list__item">
                <p>No Vehicle Section</p>
            </li>
        @endforelse
    </ul>
