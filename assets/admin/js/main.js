"use strict";
(function ($) {
  $(document).ready(function () {
    // Back To Top
    // var btn = $(".back-to-top");
    // $(window).scroll(function () {
    //   if ($(window).scrollTop() > 300) {
    //     btn.addClass("show");
    //   } else {
    //     btn.removeClass("show");
    //   }
    // });
    // btn.on("click", function (e) {
    //   e.preventDefault();
    //   $("html, body").animate({ scrollTop: 0 }, "300");
    // });

    // Add Attribute For Bg Image
    $(".bg-image").css("background", function () {
      var bg = "url(" + $(this).data("bg-image") + ")";
      return bg;
    });

    // Add active class to current active page
    function dynamicActiveMenuClass(selector) {
      let fileName = window.location.pathname.split("/").reverse()[0];
      selector.find("li").each(function () {
        let anchor = $(this).find("a");
        if ($(anchor).attr("href") == fileName) {
          $(this).addClass("active");
        }
      });
      // if any li has active element add class
      selector.children("li").each(function () {
        if ($(this).find(".active").length) {
          $(this).addClass("active");
        }
      });
      // if no file name return
      if ("" == fileName) {
        selector.find("li").eq(0).addClass("active");
      }
    }
    if ($("ul.sidebar-menu-list").length) {
      dynamicActiveMenuClass($("ul.sidebar-menu-list"));
    }

    // Password Show Hide
    $(".password-switch").on("click", function () {
      $(this).toggleClass("fa-eye");
      var input = $($(this).attr("id"));
      if (input.attr("type") == "password") {
        input.attr("type", "text");
      } else {
        input.attr("type", "password");
      }
    });

    // Sidebar Dropdown Menu Open/Close
    $(".has-dropdown > a").click(function () {
      $(".sidebar-submenu").slideUp(200);
      if ($(this).parent().hasClass("active")) {
        $(".has-dropdown").removeClass("active");
        $(this).parent().removeClass("active");
      } else {
        $(".has-dropdown").removeClass("active");
        $(this).next(".sidebar-submenu").slideDown(200);
        $(this).parent().addClass("active");
      }
    });
    $($)
      .find(".has-dropdown")
      .each(function () {
        if ($(this).hasClass("active")) {
          $(this)
            .children("a")
            .siblings(".sidebar-submenu")
            .css("display", "block");
        }
      });

    // Sidebar Open/Close
    $(".sidebar-trigger").on("click", function () {
      $(".sidebar-menu").addClass("show");
      $(".theme-overlay").addClass("show");
      $("body").addClass("scroll-disable-sm");
    });
    $(".sidebar-menu__close, .theme-overlay").on("click", function () {
      $(".sidebar-menu").removeClass("show");
      $(".theme-overlay").removeClass("show");
      $("body").removeClass("scroll-disable-sm");
    });

    // Language
    $(".language__button").on("click", function () {
      $(".language__list").toggleClass("show");
    });
    $($).on("click", function (event) {
      var target = $(event.target);

      if (
        !target.closest(".language__button").length &&
        !target.closest(".language__list").length
      ) {
        $(".language__list").removeClass("show");
      }
    });

    // Notification
    $(".notification__button").on("click", function () {
      $(".notification-list").toggleClass("show");
    });
    $($).on("click", function (event) {
      var target = $(event.target);

      if (
        !target.closest(".notification__button").length &&
        !target.closest(".notification-list").length
      ) {
        $(".notification-list").removeClass("show");
      }
    });

    // User Profile Dropdown
    $(".user-info__button").on("click", function () {
      $(".user-info-dropdown").toggleClass("show");
    });
    $($).on("click", function (event) {
      var target = $(event.target);

      if (
        !target.closest(".user-info__button").length &&
        !target.closest(".user-info-dropdown").length
      ) {
        $(".user-info-dropdown").removeClass("show");
      }
    });

    // User List Tab
    $(".theme-tab-list-trigger").on("click", function () {
      $(".theme-tab-list").toggleClass("show");
    });
    $($).on("click", function (event) {
      var target = $(event.target);

      if (
        !target.closest(".theme-tab-list-trigger").length &&
        !target.closest(".theme-tab-list").length
      ) {
        $(".theme-tab-list").removeClass("show");
      }
    });

    // Extensions buttons style class add
    $(".extension-item").each(function () {
      const item = $(this);
      const checkbox = item.find(".form-check-input");

      const buttons = item.find(".configure, .help");

      checkbox.on("change", function () {
        if ($(this).is(":checked")) {
          buttons.addClass("checked");
        } else {
          buttons.removeClass("checked");
        }
      });
    });

    // Select2 Js
    $(".js-select2").select2();
    $(".js-select2-multi").select2();

    // Color Picker
    var initialColor = "#924CFF";
    $(".colorPicker").spectrum({
      color: initialColor,
      change: function (color) {
        $(this)
          .closest(".colorPickerWrapper")
          .find(".siteColor")
          .val(color.toHex().toUpperCase());
      },
    });
    $(".siteColor").on("input", function () {
      let hex = $(this)
        .val()
        .replace(/[^0-9A-F]/gi, "")
        .substring(0, 6);
      $(this).val(hex.toUpperCase());
      if (hex.length === 6) {
        $(this)
          .closest(".colorPickerWrapper")
          .find(".colorPicker")
          .spectrum("set", "#" + hex);
      }
    });

    // Tooltips Enable
    const tooltipTriggerList = document.querySelectorAll(
      '[data-bs-toggle="tooltip"]',
    );
    const tooltipList = [...tooltipTriggerList].map(
      (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl),
    );

    $(".buildSlug").on("click", function () {
      let closestForm = $(this).closest("form");
      let title = closestForm.find("[name=name]").val();
      closestForm.find("[name=slug]").val(title);
      closestForm.find("[name=slug]").trigger("input");
    });

    $("[name=slug]").on("input", function () {
      let closestForm = $(this).closest("form");
      closestForm.find("[type=submit]").addClass("disabled");
      let slug = $(this).val();
      slug = slug
        .toLowerCase()
        .replace(/ /g, "-")
        .replace(/[^\w-]+/g, "");
      $(this).val(slug);
      if (slug) {
        $(".slug-verification").removeClass("d-none");
        $(".slug-verification").html(`
                  <small class="text__info"><i class="las la-spinner la-spin"></i> @lang('Verifying')</small>
              `);
        $.get(
          "{{ route('admin.frontend.manage.pages.check.slug',$pData->id) }}",
          { slug: slug },
          function (response) {
            if (!response.exists) {
              $(".slug-verification").html(`
                          <small class="text__success"><i class="las la-check"></i> @lang('Verified')</small>
                      `);
              closestForm.find("[type=submit]").removeClass("disabled");
            }
            if (response.exists) {
              $(".slug-verification").html(`
                          <small class="text__danger"><i class="las la-times"></i> @lang('Slug already exists')</small>
                      `);
            }
          },
        );
      } else {
        $(".slug-verification").addClass("d-none");
      }
    });

    // Summernote
    $("#descriptionEditor").summernote({
      height: 200,
      toolbar: [
        ["insert", ["link"]],
        ["insert", ["picture"]],
        ["para", ["ul", "paragraph"]],
        ["insert", ["video"]],
        ["font", ["underline", "italic", "bold"]],
        ["view", ["fullscreen"]],
        ["view", ["codeview"]],
        ["help", ["help"]],
      ],
    });
  });

  // Preloader
  $(window).on("load", function () {
    $(".theme-preloader").fadeOut();
  });

  Array.from(document.querySelectorAll("table")).forEach((table) => {
    let heading = table.querySelectorAll("thead tr th");
    Array.from(table.querySelectorAll("tbody tr")).forEach((row) => {
      let columArray = Array.from(row.querySelectorAll("td"));
      if (columArray.length <= 1) return;
      columArray.forEach((colum, i) => {
        colum.setAttribute("data-label", heading[i].innerText);
      });
    });
  });

  // Global function for Nice Select with search
  
})(jQuery);
