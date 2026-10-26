<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once __DIR__ . "/_nilai_helpers.php";
$csrfToken = nilaiCsrfToken();
require_once __DIR__ . "/connect.php";

if (!function_exists("h")) {
    function h($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
    }
}

if (!function_exists("nilaiFotoDisplayUrl")) {
    function nilaiFotoDisplayUrl($filename) {
        $demo = [
            "demo-user1.jpg" => "./assets/img/user1-128x128.jpg",
            "demo-user2.jpg" => "./assets/img/user2-160x160.jpg",
            "demo-user3.jpg" => "./assets/img/user3-128x128.jpg",
            "demo-user4.jpg" => "./assets/img/user4-128x128.jpg",
            "demo-user5.jpg" => "./assets/img/user5-128x128.jpg",
            "demo-user6.jpg" => "./assets/img/user6-128x128.jpg",
            "demo-user7.jpg" => "./assets/img/user7-128x128.jpg",
            "demo-user8.jpg" => "./assets/img/user8-128x128.jpg",
        ];
        $filename = is_string($filename) ? $filename : "";
        if (isset($demo[$filename])) return $demo[$filename];
        if (preg_match('/^[a-f0-9]{32}\\.(?:jpg|png|webp)$/D', $filename)) {
            return "./uploads/foto-mahasiswa/" . rawurlencode($filename);
        }
        return "./assets/img/default-150x150.png";
    }
}

$flash = $_SESSION["nilai_flash"] ?? null;
$formErrors = $_SESSION["nilai_form_errors"] ?? [];
$formValues = $_SESSION["nilai_form_values"] ?? ["nim" => "", "namamhsw" => "", "nilaiakhir" => "", "nim_lama" => "", "foto_lama" => ""];
$openModal = $_SESSION["nilai_open_modal"] ?? "";
unset($_SESSION["nilai_flash"], $_SESSION["nilai_form_errors"], $_SESSION["nilai_form_values"], $_SESSION["nilai_open_modal"]);

$nilaiRows = [];
$loadError = "";
try {
    $result = mysqli_query($conn, "SELECT * FROM nilaimhsw ORDER BY nilaiakhir DESC, namamhsw ASC, nim ASC");
    while ($record = mysqli_fetch_assoc($result)) {
        $nilaiRows[] = $record;
    }
    mysqli_free_result($result);
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    $loadError = "Data nilai belum dapat dimuat. Pastikan tabel nilaimhsw tersedia pada database db5a.";
}

mysqli_close($conn);
?>

<!doctype html>
<html lang="id">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE 4 | Nilai Mahasiswa</title>

    <!--begin::Theme Init (prevents flash of incorrect theme on load, #6043)-->
    <script>
      (() => {
        'use strict';
        const root = document.documentElement;

        // Applications with their own theming opt out of AdminLTE's color mode
        // entirely, here as well as in the bundle.
        if (root.getAttribute('data-lte-color-mode') === 'off') {
          return;
        }

        const STORAGE_KEY = 'lte-theme';
        let stored = null;
        try {
          stored = localStorage.getItem(STORAGE_KEY);
        } catch {
          // localStorage may be unavailable (private mode, sandboxed iframe).
        }
        // Mirror the precedence in color-mode.ts: the visitor's stored choice
        // wins, then a theme this page declared itself, then the OS preference.
        const authored = root.getAttribute('data-bs-theme');
        let resolved = 'light';
        if (stored === 'dark' || stored === 'light') {
          resolved = stored;
        } else if (authored === 'dark' || authored === 'light') {
          resolved = authored;
        } else if (globalThis.matchMedia('(prefers-color-scheme: dark)').matches) {
          resolved = 'dark';
        }
        root.setAttribute('data-bs-theme', resolved);
        root.style.colorScheme = resolved;
        // Flag values computed here, so the bundle does not mistake them for a
        // theme the page declared and stop following the OS preference.
        if (resolved !== authored) {
          root.setAttribute('data-lte-theme-resolved', '');
        }
      })();
    </script>
    <!--end::Theme Init-->

    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <!--end::Accessibility Meta Tags-->

    <!--begin::Primary Meta Tags-->
    <meta name="title" content="AdminLTE 4 | Nilai Mahasiswa" />
    <meta name="author" content="ColorlibHQ" />
    <meta
      name="description"
      content="AdminLTE is a free Bootstrap 5 admin dashboard template with almost 50 example pages, built with vanilla JS and designed with accessibility in mind."
    />
    <meta
      name="keywords"
      content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel"
    />
    <!--end::Primary Meta Tags-->

    <!--begin::Accessibility Features-->
    <!-- Skip links will be dynamically added by accessibility.js -->
    <meta name="supported-color-schemes" content="light dark" />
    <link rel="preload" href="./css/adminlte.css" as="style" />
    <!--end::Accessibility Features-->

    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
      media="print"
      onload="this.media = 'all'"
    />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="./css/adminlte.css" />
    <!--end::Required Plugin(AdminLTE)-->
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <nav class="app-header navbar navbar-expand bg-body">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Start Navbar Links-->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a
                class="nav-link"
                data-lte-toggle="sidebar"
                href="#"
                role="button"
                aria-label="Toggle sidebar"
              >
                <i class="bi bi-list"></i>
              </a>
            </li>

            <li class="nav-item d-none d-md-block">
              <a href="./index.html" class="nav-link">
                <i class="bi bi-grid-1x2 me-1" aria-hidden="true"></i>
                Live preview
              </a>
            </li>
            <li class="nav-item d-none d-md-block">
              <a href="./docs/introduction.html" class="nav-link">
                <i class="bi bi-book me-1" aria-hidden="true"></i>
                Documentation
              </a>
            </li>
          </ul>
          <!--end::Start Navbar Links-->

          <!--begin::Navbar Search-->
          <form
            class="navbar-search d-none d-md-block ms-3"
            role="search"
            action="./pages/search-results.html"
          >
            <label for="navbar-search-input" class="visually-hidden">Search</label>
            <div class="navbar-search-field">
              <input
                type="search"
                id="navbar-search-input"
                name="q"
                class="form-control"
                placeholder="Search…"
                autocomplete="off"
              />
              <button class="navbar-search-submit" type="submit" aria-label="Submit search">
                <i class="bi bi-search" aria-hidden="true"></i>
              </button>
            </div>
          </form>
          <!--end::Navbar Search-->

          <!--begin::End Navbar Links-->
          <ul class="navbar-nav ms-auto">
            <!--begin::Search (small screens: the field above is hidden, so link to the search page)-->
            <li class="nav-item d-md-none">
              <a class="nav-link" href="./pages/search-results.html" aria-label="Search">
                <i class="bi bi-search" aria-hidden="true"></i>
              </a>
            </li>
            <!--end::Search-->
            <!--begin::Messages Dropdown Menu-->
            <li class="nav-item dropdown">
              <a
                class="nav-link"
                data-bs-toggle="dropdown"
                href="#"
                aria-label="Messages: 3 unread"
              >
                <i class="bi bi-chat-text"></i>
                <span class="navbar-badge badge text-bg-danger">3</span>
              </a>
              <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <a href="#" class="dropdown-item">
                  <!--begin::Message-->
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <img
                        src="./assets/img/user1-128x128.jpg"
                        alt=""
                        class="img-size-50 rounded-circle me-3"
                      />
                    </div>
                    <div class="flex-grow-1">
                      <p class="dropdown-item-title">
                        Brad Diesel
                        <span class="float-end fs-7 text-danger"
                          ><i class="bi bi-star-fill"></i
                        ></span>
                      </p>
                      <p class="fs-7">Call me whenever you can...</p>
                      <p class="fs-7 text-secondary">
                        <i class="bi bi-clock-fill me-1"></i> 4 Hours Ago
                      </p>
                    </div>
                  </div>
                  <!--end::Message-->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                  <!--begin::Message-->
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <img
                        src="./assets/img/user8-128x128.jpg"
                        alt=""
                        class="img-size-50 rounded-circle me-3"
                      />
                    </div>
                    <div class="flex-grow-1">
                      <p class="dropdown-item-title">
                        John Pierce
                        <span class="float-end fs-7 text-secondary">
                          <i class="bi bi-star-fill"></i>
                        </span>
                      </p>
                      <p class="fs-7">I got your message bro</p>
                      <p class="fs-7 text-secondary">
                        <i class="bi bi-clock-fill me-1"></i> 4 Hours Ago
                      </p>
                    </div>
                  </div>
                  <!--end::Message-->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                  <!--begin::Message-->
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <img
                        src="./assets/img/user3-128x128.jpg"
                        alt=""
                        class="img-size-50 rounded-circle me-3"
                      />
                    </div>
                    <div class="flex-grow-1">
                      <p class="dropdown-item-title">
                        Nora Silvester
                        <span class="float-end fs-7 text-warning">
                          <i class="bi bi-star-fill"></i>
                        </span>
                      </p>
                      <p class="fs-7">The subject goes here</p>
                      <p class="fs-7 text-secondary">
                        <i class="bi bi-clock-fill me-1"></i> 4 Hours Ago
                      </p>
                    </div>
                  </div>
                  <!--end::Message-->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
              </div>
            </li>
            <!--end::Messages Dropdown Menu-->

            <!--begin::Notifications Dropdown Menu-->
            <li class="nav-item dropdown">
              <a
                class="nav-link"
                data-bs-toggle="dropdown"
                href="#"
                aria-label="Notifications: 15 unread"
              >
                <i class="bi bi-bell-fill"></i>
                <span class="navbar-badge badge text-bg-warning">15</span>
              </a>
              <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <span class="dropdown-item dropdown-header">15 Notifications</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                  <i class="bi bi-envelope me-2"></i> 4 new messages
                  <span class="float-end text-secondary fs-7">3 mins</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                  <i class="bi bi-people-fill me-2"></i> 8 friend requests
                  <span class="float-end text-secondary fs-7">12 hours</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                  <i class="bi bi-file-earmark-fill me-2"></i> 3 new reports
                  <span class="float-end text-secondary fs-7">2 days</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer"> See All Notifications </a>
              </div>
            </li>
            <!--end::Notifications Dropdown Menu-->

            <!--begin::Language Menu-->
            <!-- Markup only: swapping the locale is the application's job. The docs
           Recipes page shows how to wire this to a real locale switch. -->
            <li class="nav-item dropdown">
              <a
                class="nav-link"
                href="#"
                id="language-menu"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                aria-label="Change language, current language English"
              >
                <i class="bi bi-translate" aria-hidden="true"></i>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="language-menu">
                <li>
                  <a class="dropdown-item active" href="#" hreflang="en" aria-current="true">
                    English
                    <i class="bi bi-check-lg ms-2" aria-hidden="true"></i>
                  </a>
                </li>
                <li><a class="dropdown-item" href="#" hreflang="es">Español</a></li>
                <li><a class="dropdown-item" href="#" hreflang="fr">Français</a></li>
                <li><a class="dropdown-item" href="#" hreflang="de">Deutsch</a></li>
                <li><a class="dropdown-item" href="#" hreflang="ar">العربية</a></li>
              </ul>
            </li>
            <!--end::Language Menu-->

            <!--begin::Fullscreen Toggle-->
            <li class="nav-item">
              <a
                class="nav-link"
                href="#"
                data-lte-toggle="fullscreen"
                aria-label="Toggle fullscreen"
              >
                <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                <i data-lte-icon="minimize" class="bi bi-fullscreen-exit d-none"></i>
              </a>
            </li>
            <!--end::Fullscreen Toggle-->

            <!--begin::Color Mode Toggle (#6010)-->
            <li class="nav-item dropdown">
              <a
                class="nav-link"
                href="#"
                id="bd-theme"
                aria-label="Toggle color scheme"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                <i class="bi bi-sun-fill" data-lte-theme-icon="light"></i>
                <i class="bi bi-moon-fill d-none" data-lte-theme-icon="dark"></i>
                <i class="bi bi-circle-half d-none" data-lte-theme-icon="auto"></i>
              </a>
              <ul
                class="dropdown-menu dropdown-menu-end"
                aria-labelledby="bd-theme"
                style="--bs-dropdown-min-width: 8rem"
              >
                <li>
                  <button
                    type="button"
                    class="dropdown-item d-flex align-items-center"
                    data-bs-theme-value="light"
                    aria-pressed="false"
                  >
                    <i class="bi bi-sun-fill me-2"></i>
                    Light
                    <i class="bi bi-check-lg ms-auto d-none"></i>
                  </button>
                </li>
                <li>
                  <button
                    type="button"
                    class="dropdown-item d-flex align-items-center"
                    data-bs-theme-value="dark"
                    aria-pressed="false"
                  >
                    <i class="bi bi-moon-fill me-2"></i>
                    Dark
                    <i class="bi bi-check-lg ms-auto d-none"></i>
                  </button>
                </li>
                <li>
                  <button
                    type="button"
                    class="dropdown-item d-flex align-items-center active"
                    data-bs-theme-value="auto"
                    aria-pressed="true"
                  >
                    <i class="bi bi-circle-half me-2"></i>
                    Auto
                    <i class="bi bi-check-lg ms-auto d-none"></i>
                  </button>
                </li>
              </ul>
            </li>
            <!--end::Color Mode Toggle-->

            <!--begin::User Menu Dropdown-->
            <li class="nav-item dropdown user-menu">
              <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <img
                  src="./assets/img/user2-160x160.jpg"
                  class="user-image rounded-circle shadow"
                  alt="Admin Nilai"
                />
                <span class="d-none d-md-inline">Admin Nilai</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <!--begin::User Image-->
                <li class="user-header text-bg-primary">
                  <img
                    src="./assets/img/user2-160x160.jpg"
                    class="rounded-circle shadow"
                    alt="Admin Nilai"
                  />
                  <p>
                    Pengelola Nilai Mahasiswa
                    <small>Member since Nov. 2023</small>
                  </p>
                </li>
                <!--end::User Image-->
                <!--begin::Menu Body-->
                <li class="user-body">
                  <!--begin::Row-->
                  <div class="row">
                    <div class="col-4 text-center">
                      <a href="#">Followers</a>
                    </div>
                    <div class="col-4 text-center">
                      <a href="#">Sales</a>
                    </div>
                    <div class="col-4 text-center">
                      <a href="#">Friends</a>
                    </div>
                  </div>
                  <!--end::Row-->
                </li>
                <!--end::Menu Body-->
                <!--begin::Menu Footer-->
                <li class="user-footer">
                  <a href="#" class="btn btn-outline-secondary">Profile</a>
                  <a href="#" class="btn btn-outline-danger float-end">Sign out</a>
                </li>
                <!--end::Menu Footer-->
              </ul>
            </li>
            <!--end::User Menu Dropdown-->
          </ul>
          <!--end::End Navbar Links-->
        </div>
        <!--end::Container-->
      </nav>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <!--begin::Sidebar Brand-->
        <div class="sidebar-brand">
          <!--begin::Brand Link-->
          <a href="./index.html" class="brand-link">
            <!--begin::Brand Image-->
            <img
              src="./assets/img/AdminLTELogo.png"
              alt="AdminLTE Logo"
              class="brand-image opacity-75 shadow"
            />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">AdminLTE 4</span>
            <!--end::Brand Text-->
          </a>
          <!--end::Brand Link-->
        </div>
        <!--end::Sidebar Brand-->
        <!--begin::Sidebar Search-->
        <div class="sidebar-search" role="search">
          <label for="sidebar-search-input" class="visually-hidden">Filter menu</label>
          <input
            type="search"
            id="sidebar-search-input"
            class="form-control form-control-sm"
            placeholder="Filter menu…"
            autocomplete="off"
            data-lte-toggle="sidebar-search"
            data-lte-target="#navigation"
          />
          <p class="fs-7 text-secondary mt-2 mb-0" data-lte-search-empty role="status" hidden>
            No matching pages.
          </p>
        </div>
        <!--end::Sidebar Search-->
        <!--begin::Sidebar Wrapper-->
        <div class="sidebar-wrapper">
          <nav class="mt-2" aria-label="Main navigation">
            <!--begin::Sidebar Menu-->
            <ul
              class="nav sidebar-menu flex-column"
              data-lte-toggle="treeview"
              data-accordion="false"
              id="navigation"
            >
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-speedometer"></i>
                  <p>
                    Dashboard
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./index.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Dashboard v1</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./index2.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Dashboard v2</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./index3.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Dashboard v3</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="./starter.html" class="nav-link">
                  <i class="nav-icon bi bi-file-earmark"></i>
                  <p>Starter Page</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./generate/theme.html" class="nav-link">
                  <i class="nav-icon bi bi-palette"></i>
                  <p>Theme Generate</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-box-seam-fill"></i>
                  <p>
                    Widgets
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./widgets/small-box.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Small Box</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./widgets/info-box.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>info Box</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./widgets/cards.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Cards</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./widgets/social.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Social &amp; Post</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-clipboard-fill"></i>
                  <p>
                    Layout Options
                    <span class="nav-badge badge text-bg-secondary me-3">12</span>
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./layout/unfixed-sidebar.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Default Sidebar</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./layout/fixed-sidebar.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Fixed Sidebar</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./layout/fixed-header.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Fixed Header</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./layout/fixed-footer.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Fixed Footer</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./layout/fixed-complete.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Fixed Complete</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./layout/layout-custom-area.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Layout <small>+ Custom Area </small></p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./layout/sidebar-mini.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Sidebar Mini</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./layout/collapsed-sidebar.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Sidebar Mini <small>+ Collapsed</small></p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./layout/collapsed-sidebar-without-hover.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Sidebar Mini <small>+ Collapsed + No Hover</small></p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./layout/logo-switch.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Sidebar Mini <small>+ Logo Switch</small></p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./layout/top-nav.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Top Nav <small>+ No Sidebar</small></p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./layout/layout-rtl.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Layout RTL</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-tree-fill"></i>
                  <p>
                    UI Elements
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./UI/general.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>General</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./UI/icons.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Icons</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./UI/timeline.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Timeline</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./UI/ribbons.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Ribbons</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./UI/colors.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Colors</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-envelope"></i>
                  <p>
                    Mailbox
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./mailbox/inbox.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Inbox</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./mailbox/read.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Read Message</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./mailbox/compose.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Compose</p>
                    </a>
                  </li>
                </ul>
              </li>

              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-pencil-square"></i>
                  <p>
                    Forms
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./forms/elements.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Elements</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./forms/layout.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Layout</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./forms/validation.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Validation</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./forms/wizard.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Wizard</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./forms/advanced.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Advanced Elements</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./forms/editors.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Editors</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-table"></i>
                  <p>
                    Tables
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./tables/simple.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Simple Tables</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./tables/data.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Data Tables</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-graph-up"></i>
                  <p>
                    Charts
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./charts/chartjs.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Chart.js</p>
                    </a>
                  </li>
                </ul>
              </li>

              <li class="nav-header">PAGES</li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-file-earmark-text"></i>
                  <p>
                    Pages
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="./pages/profile.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Profile</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./pages/settings.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Settings</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./pages/invoice.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Invoice</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./pages/calendar.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Calendar</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./pages/kanban.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Kanban</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./pages/chat.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Chat</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./pages/file-manager.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>File Manager</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./pages/projects.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Projects</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./pages/gallery.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Gallery</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./pages/search-results.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Search Results</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./pages/pricing.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Pricing</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./pages/faq.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>FAQ</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>
                        Error
                        <i class="nav-arrow bi bi-chevron-right"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="./pages/404.html" class="nav-link">
                          <i class="nav-icon bi bi-circle"></i>
                          <p>404</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="./pages/500.html" class="nav-link">
                          <i class="nav-icon bi bi-circle"></i>
                          <p>500</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="./pages/maintenance.html" class="nav-link">
                          <i class="nav-icon bi bi-circle"></i>
                          <p>Maintenance</p>
                        </a>
                      </li>
                    </ul>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="./users.php" class="nav-link active">
                  <i class="nav-icon bi bi-people"></i>
                  <p>Nilai Mahasiswa</p>
                </a>
              </li>

              <li class="nav-header">EXAMPLES</li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-box-arrow-in-right"></i>
                  <p>
                    Auth
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="nav-icon bi bi-box-arrow-in-right"></i>
                      <p>
                        Version 1
                        <i class="nav-arrow bi bi-chevron-right"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="./examples/login.html" class="nav-link">
                          <i class="nav-icon bi bi-circle"></i>
                          <p>Login</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="./examples/register.html" class="nav-link">
                          <i class="nav-icon bi bi-circle"></i>
                          <p>Register</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="./examples/forgot-password.html" class="nav-link">
                          <i class="nav-icon bi bi-circle"></i>
                          <p>Forgot Password</p>
                        </a>
                      </li>
                    </ul>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="nav-icon bi bi-box-arrow-in-right"></i>
                      <p>
                        Version 2
                        <i class="nav-arrow bi bi-chevron-right"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="./examples/login-v2.html" class="nav-link">
                          <i class="nav-icon bi bi-circle"></i>
                          <p>Login</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="./examples/register-v2.html" class="nav-link">
                          <i class="nav-icon bi bi-circle"></i>
                          <p>Register</p>
                        </a>
                      </li>
                    </ul>
                  </li>
                  <li class="nav-item">
                    <a href="./examples/lockscreen.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Lockscreen</p>
                    </a>
                  </li>
                </ul>
              </li>

              <li class="nav-header">MULTI LEVEL EXAMPLE</li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-circle-fill"></i>
                  <p>Level 1</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-circle-fill"></i>
                  <p>
                    Level 1
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Level 2</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>
                        Level 2
                        <i class="nav-arrow bi bi-chevron-right"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="#" class="nav-link">
                          <i class="nav-icon bi bi-record-circle-fill"></i>
                          <p>Level 3</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="#" class="nav-link">
                          <i class="nav-icon bi bi-record-circle-fill"></i>
                          <p>Level 3</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="#" class="nav-link">
                          <i class="nav-icon bi bi-record-circle-fill"></i>
                          <p>Level 3</p>
                        </a>
                      </li>
                    </ul>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Level 2</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-circle-fill"></i>
                  <p>Level 1</p>
                </a>
              </li>

              <li class="nav-header">LABELS</li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-circle text-danger"></i>
                  <p class="text">Important</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-circle text-warning"></i>
                  <p>Warning</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-circle text-info"></i>
                  <p>Informational</p>
                </a>
              </li>
            </ul>
            <!--end::Sidebar Menu-->

            <!-- Docs CTA (bottom of sidebar) -->
            <div class="p-3 mt-3 border-top border-secondary border-opacity-25">
              <a
                href="./docs/introduction.html"
                class="btn btn-sm btn-outline-light w-100 d-flex align-items-center justify-content-center gap-2"
              >
                <i class="bi bi-book" aria-hidden="true"></i>
                View documentation
              </a>
            </div>
          </nav>
        </div>
        <!--end::Sidebar Wrapper-->
      </aside>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6">
                <h1 class="mb-0 fs-3">Nilai Mahasiswa</h1>
              </div>
              <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="#">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nilai Mahasiswa</li>
                  </ol>
                </nav>
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <?php if (is_array($flash) && !empty($flash["message"])): ?>
              <div class="alert alert-<?= h($flash["type"] ?? "info") ?> alert-dismissible fade show" role="alert">
                <?= h($flash["message"]) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
              </div>
            <?php endif; ?>
            <?php if ($loadError !== ""): ?>
              <div class="alert alert-danger" role="alert"><?= h($loadError) ?></div>
            <?php endif; ?>
            <!--begin::Row-->
            <div class="row">
              <div class="col-12">
                <!--begin::Card-->
                <div class="card mb-4">
                  <!--begin::Card Header-->
                  <div class="card-header">
                    <div class="row g-2 align-items-center">
                      <div class="col-12 col-md-4">
                        <h3 class="card-title">Rekap Nilai Mahasiswa</h3>
                      </div>
                      <div class="col-12 col-md-8">
                        <div class="d-flex flex-wrap justify-content-md-end gap-2">
                          <div class="input-group input-group-sm w-auto">
                            <span class="input-group-text">
                              <i class="bi bi-search" aria-hidden="true"></i>
                            </span>
                            <input
                              type="search"
                              id="nilai-search"
                              class="form-control"
                              placeholder="Cari nama mahasiswa"
                              aria-label="Cari nama mahasiswa"
                              style="width: 180px"
                            />
                          </div>
                          <select
                            id="nilai-predikat-filter"
                            class="form-select form-select-sm w-auto"
                            aria-label="Saring berdasarkan predikat"
                          >
                            <option value="all" selected>Semua Predikat</option>
                            <option value="sangat memuaskan">Sangat Memuaskan</option>
                            <option value="memuaskan">Memuaskan</option>
                            <option value="cukup memuaskan">Cukup Memuaskan</option>
                            <option value="kurang memuaskan">Kurang Memuaskan</option>
                            <option value="tidak lulus">Tidak Lulus</option>
                          </select>
                          <button
                            type="button"
                            class="btn btn-sm btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#modal-add-user"
                          >
                            <i class="bi bi-person-plus-fill me-1" aria-hidden="true"> </i>
                            Tambah Data Nilai
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!--end::Card Header-->
                  <!--begin::Card Body-->
                  <div class="card-body p-0">
                    <div class="table-responsive">
                      <table class="table table-hover align-middle m-0">
                        <thead>
                          <tr>
                            <th>NIM</th>
                            <th>Nama Lengkap</th>
                            <th scope="col" id="nilai-sort-header" aria-sort="descending">
                              <button type="button" id="nilai-sort-button" class="btn btn-link p-0 text-body text-decoration-none fw-semibold" aria-label="Urutkan nilai akhir, saat ini terbesar ke terkecil" title="Klik untuk mengubah urutan nilai">
                                Nilai Akhir <i id="nilai-sort-icon" class="bi bi-arrow-down-short ms-1" aria-hidden="true"></i>
                              </button>
                            </th>
                            <th>Nilai Huruf</th>
                            <th>Predikat</th>
                            <th>Tgl Dibuat</th>
                            <th>Tgl Diubah</th>
                            <th class="text-end">Aksi</th>
                          </tr>
                        </thead>
                        <tbody id="nilai-table-body">
                          <?php if (!empty($nilaiRows)): ?>
                            <?php foreach ($nilaiRows as $row): ?>
                              <?php
                                $rowNim = (string) ($row["nim"] ?? "");
                                $rowNama = (string) ($row["namamhsw"] ?? "");
                                $rowNilai = (string) ($row["nilaiakhir"] ?? "");
                                $rowHuruf = strtoupper(trim((string) ($row["nilaihuruf"] ?? "")));
                                $rowPredikat = (string) ($row["predikat"] ?? "");
                                $rowTanggal = (string) ($row["created_date"] ?? $row["created_at"] ?? "-");
                                $rowTanggalDiubah = trim((string) ($row["modified_date"] ?? "")) !== ""
                                    ? (string) $row["modified_date"]
                                    : "Belum diubah";
                                $rowFoto = (string) ($row["foto"] ?? "");
                                $rowFotoUrl = nilaiFotoDisplayUrl($rowFoto);
                                $badgeClass = [
                                    "A" => "text-bg-success",
                                    "B" => "text-bg-primary",
                                    "C" => "text-bg-info",
                                    "D" => "text-bg-warning",
                                    "E" => "text-bg-danger"
                                ][$rowHuruf] ?? "text-bg-secondary";
                              ?>
                              <tr class="nilai-row"
                                  data-search="<?= h($rowNama) ?>"
                                  data-nilai="<?= h(number_format((float) $rowNilai, 2, ".", "")) ?>"
                                  data-grade="<?= h($rowHuruf) ?>"
                                  data-predikat="<?= h(mb_strtolower($rowPredikat, "UTF-8")) ?>">
                                <td><span class="font-monospace fw-semibold"><?= h($rowNim) ?></span></td>
                                <td>
                                  <div class="d-flex align-items-center">
                                    <img src="<?= h($rowFotoUrl) ?>" alt="Foto <?= h($rowNama) ?>"
                                         class="rounded-circle me-2" width="36" height="36" loading="lazy"
                                         style="object-fit: cover; flex-shrink: 0;"
                                         onerror="this.onerror=null;this.src='./assets/img/default-150x150.png';" />
                                    <span class="fw-medium"><?= h($rowNama) ?></span>
                                  </div>
                                </td>
                                <td><?= h(number_format((float) $rowNilai, 2, ".", "")) ?></td>
                                <td><span class="badge <?= h($badgeClass) ?>"><?= h($rowHuruf) ?></span></td>
                                <td><?= h($rowPredikat) ?></td>
                                <td><?= h($rowTanggal !== "" ? $rowTanggal : "-") ?></td>
                                <td><?= h($rowTanggalDiubah) ?></td>
                                <td class="text-end">
                                  <div class="btn-group btn-group-sm">
                                    <button
                                      type="button"
                                      class="btn btn-outline-primary"
                                      data-bs-toggle="modal"
                                      data-bs-target="#modal-edit-user"
                                      data-nim="<?= h($rowNim) ?>"
                                      data-namamhsw="<?= h($rowNama) ?>"
                                      data-nilaiakhir="<?= h($rowNilai) ?>"
                                      data-foto="<?= h($rowFoto ?? "") ?>"
                                      data-foto-url="<?= h($rowFotoUrl ?? "./assets/img/default-150x150.png") ?>"
                                      aria-label="Edit nilai NIM <?= h($rowNim) ?>"
                                      title="Edit nilai">
                                      <i class="bi bi-pencil" aria-hidden="true"></i>
                                    </button>
                                    <button
                                      type="button"
                                      class="btn btn-outline-danger"
                                      data-bs-toggle="modal"
                                      data-bs-target="#modal-delete-user"
                                      data-nim="<?= h($rowNim) ?>"
                                      aria-label="Hapus nilai NIM <?= h($rowNim) ?>"
                                      title="Hapus nilai">
                                      <i class="bi bi-trash" aria-hidden="true"></i>
                                    </button>
                                  </div>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <tr id="nilai-empty-state">
                              <td colspan="8" class="text-center text-body-secondary py-4">
                                Belum ada data nilai mahasiswa. Klik <strong>Tambah Data Nilai</strong> untuk mulai.
                              </td>
                            </tr>
                          <?php endif; ?>
                          <tr id="nilai-no-match" class="d-none">
                            <td colspan="7" class="text-center text-body-secondary py-4">Tidak ada data yang sesuai dengan pencarian.</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <!-- /.table-responsive -->
                  </div>
                  <!--end::Card Body-->
                  <!--begin::Card Footer-->
                  <div class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="pt-1 fs-7 text-body-secondary" id="nilai-summary">
                      Total <?= count($nilaiRows) ?> data nilai
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                      <label class="form-label mb-0 small" for="nilai-page-size">Data per halaman</label>
                      <select id="nilai-page-size" class="form-select form-select-sm w-auto" aria-label="Jumlah data per halaman">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="15">15</option>
                      </select>
                      <nav aria-label="Navigasi halaman data nilai">
                        <ul class="pagination pagination-sm m-0" id="nilai-pagination"></ul>
                      </nav>
                    </div>
                  </div>
                  <!--end::Card Footer-->
                </div>
                <!--end::Card-->
              </div>
              <!-- /.col -->
            </div>
            <!--end::Row-->

            <!--begin::Add Nilai Modal-->
            <div class="modal fade" id="modal-add-user" tabindex="-1" aria-labelledby="modal-add-user-label" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form action="./insert_nilai.php" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
                    <input type="hidden" name="csrf_token" value="<?= h($csrfToken) ?>" />
                    <div class="modal-header">
                      <h5 class="modal-title" id="modal-add-user-label">Tambah Data Nilai</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                      <?php if ($openModal === "add" && !empty($formErrors)): ?>
                        <div class="alert alert-danger py-2" role="alert">
                          <?php foreach ($formErrors as $message): ?>
                            <div><?= h($message) ?></div>
                          <?php endforeach; ?>
                        </div>
                      <?php endif; ?>
                      <div class="mb-3">
                        <label for="new-nilai-nim" class="form-label">NIM <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="new-nilai-nim" name="nim"
                               inputmode="numeric" pattern="[0-9]+" minlength="1" maxlength="30"
                               value="<?= h($formValues["nim"] ?? "") ?>" placeholder="Masukkan NIM" required />
                        <div class="form-text">NIM wajib diisi dan hanya boleh berisi angka.</div>
                      </div>
                      <div class="mb-3">
                        <label for="new-nilai-nama" class="form-label">Nama Mahasiswa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="new-nilai-nama" name="namamhsw"
                               value="<?= h($formValues["namamhsw"] ?? "") ?>" placeholder="Masukkan nama lengkap" required />
                      </div>
                      <div class="mb-3">
                        <label for="new-nilai-akhir" class="form-label">Nilai Akhir <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="new-nilai-akhir" name="nilaiakhir"
                               min="0" max="100" step="0.01" value="<?= h($formValues["nilaiakhir"] ?? "") ?>"
                               placeholder="0–100" required />
                      </div>
                      <div class="mb-3">
                        <label for="new-nilai-foto" class="form-label">Foto Mahasiswa</label>
                        <div class="d-flex align-items-center gap-3">
                          <img id="new-nilai-foto-preview" src="./assets/img/default-150x150.png" alt="Pratinjau foto mahasiswa" class="rounded-circle border" width="64" height="64" style="object-fit: cover; flex-shrink: 0;" />
                          <input type="file" class="form-control" id="new-nilai-foto" name="foto" accept="image/jpeg,image/png,image/webp" />
                        </div>
                        <?php if ($openModal === "add" && isset($formErrors["foto"])): ?>
                          <div class="text-danger small mt-1"><?= h($formErrors["foto"]) ?></div>
                        <?php endif; ?>
                        <div class="form-text">Opsional. Format JPG, PNG, WEBP; maksimal 2 MiB.</div>
                      </div>
                      <div class="form-text">Nilai huruf dan predikat dihitung otomatis saat data disimpan.</div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1" aria-hidden="true"></i>Simpan Nilai</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <!--end::Add Nilai Modal-->

            <!--begin::Edit Nilai Modal-->
            <div class="modal fade" id="modal-edit-user" tabindex="-1" aria-labelledby="modal-edit-user-label" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form action="./edit_nilai.php" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
                    <input type="hidden" name="csrf_token" value="<?= h($csrfToken) ?>" />
                    <input type="hidden" name="nim_lama" id="edit-nilai-nim-lama" value="<?= h($formValues["nim_lama"] ?? "") ?>" />
                    <input type="hidden" name="foto_lama" id="edit-nilai-foto-lama" value="<?= h($formValues["foto_lama"] ?? "") ?>" />
                    <div class="modal-header">
                      <h5 class="modal-title" id="modal-edit-user-label">Perbarui Data Nilai</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                      <?php if ($openModal === "edit" && !empty($formErrors)): ?>
                        <div class="alert alert-danger py-2" role="alert">
                          <?php foreach ($formErrors as $message): ?><div><?= h($message) ?></div><?php endforeach; ?>
                        </div>
                      <?php endif; ?>
                      <div class="mb-3">
                        <label for="edit-nilai-nim" class="form-label">NIM <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit-nilai-nim" name="nim"
                               inputmode="numeric" pattern="[0-9]+" minlength="1" maxlength="30"
                               value="<?= h($formValues["nim"] ?? "") ?>" required />
                      </div>
                      <div class="mb-3">
                        <label for="edit-nilai-nama" class="form-label">Nama Mahasiswa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit-nilai-nama" name="namamhsw"
                               value="<?= h($formValues["namamhsw"] ?? "") ?>" required />
                      </div>
                      <div class="mb-3">
                        <label for="edit-nilai-akhir" class="form-label">Nilai Akhir <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit-nilai-akhir" name="nilaiakhir"
                               min="0" max="100" step="0.01"
                               value="<?= h($formValues["nilaiakhir"] ?? "") ?>" required />
                      </div>
                      <div class="mb-3">
                        <label for="edit-nilai-foto" class="form-label">Foto Mahasiswa</label>
                        <div class="d-flex align-items-center gap-3">
                          <img id="edit-nilai-foto-preview" src="<?= h(nilaiFotoDisplayUrl((string)($formValues["foto_lama"] ?? ""))) ?>" alt="Pratinjau foto mahasiswa" class="rounded-circle border" width="64" height="64" style="object-fit: cover; flex-shrink: 0;" />
                          <input type="file" class="form-control" id="edit-nilai-foto" name="foto" accept="image/jpeg,image/png,image/webp" />
                        </div>
                        <?php if ($openModal === "edit" && isset($formErrors["foto"])): ?>
                          <div class="text-danger small mt-1"><?= h($formErrors["foto"]) ?></div>
                        <?php endif; ?>
                        <div class="form-text">Opsional. Pilih foto baru jika ingin mengganti foto yang ada. Maksimal 2 MiB.</div>
                      </div>
                      <div class="form-text">Nilai huruf dan predikat akan dihitung ulang berdasarkan nilai akhir terbaru.</div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1" aria-hidden="true"></i>Simpan Perubahan</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <!--end::Edit Nilai Modal-->

            <!--begin::Delete Nilai Modal-->
            <div class="modal fade" id="modal-delete-user" tabindex="-1" aria-labelledby="modal-delete-user-label" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form action="./delete_nilai.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?= h($csrfToken) ?>" />
                    <div class="modal-header">
                      <h5 class="modal-title" id="modal-delete-user-label">Hapus Data Nilai</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                      <p class="mb-0">Anda yakin akan menghapus data ini? Aksi ini tidak dapat dikembalikan!</p>
                      <input type="hidden" name="nim" id="delete-nilai-nim" value="" required />
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-danger">Hapus Nilai</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <!--end::Delete Nilai Modal-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <footer class="app-footer">
        <!--begin::To the end-->
        <div class="float-end d-none d-sm-inline">Sistem Informasi Nilai Mahasiswa</div>
        <!--end::To the end-->
        <!--begin::Copyright-->
        <strong>
          Copyright &copy; 2014-2026&nbsp;
          <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
        </strong>
        Seluruh hak cipta dilindungi.
        <!--end::Copyright-->
      </footer>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="./js/adminlte.js"></script>
    <!--end::Required Plugin(AdminLTE)-->
    <!--begin::OverlayScrollbars Configure-->
    <script>
      const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
      };
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);

        // Disable OverlayScrollbars on mobile devices to prevent touch interference
        const isMobile = window.innerWidth <= 992;

        if (
          sidebarWrapper &&
          OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined &&
          !isMobile
        ) {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: Default.scrollbarTheme,
              autoHide: Default.scrollbarAutoHide,
              clickScroll: Default.scrollbarClickScroll,
            },
          });
        }
      });
    </script>
    <!--end::OverlayScrollbars Configure-->

    <!--begin::Color Mode Toggle-->
    <!-- The light/dark/auto switcher ships in adminlte.js as the ColorMode
     module (since 4.1) — no page script needed. Only the no-flash snippet
     in <head> stays inline, because it must run before first paint. -->
    <!--end::Color Mode Toggle-->

    <!--end::Script-->

    <!--begin::Nilai Mahasiswa interactions-->
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('nilai-search');
        const predikatFilter = document.getElementById('nilai-predikat-filter');
        const pageSizeSelect = document.getElementById('nilai-page-size');
        const sortButton = document.getElementById('nilai-sort-button');
        const sortHeader = document.getElementById('nilai-sort-header');
        const sortIcon = document.getElementById('nilai-sort-icon');
        const tableBody = document.getElementById('nilai-table-body');
        const summary = document.getElementById('nilai-summary');
        const pagination = document.getElementById('nilai-pagination');
        const emptyState = document.getElementById('nilai-empty-state');
        const noMatch = document.getElementById('nilai-no-match');
        const rows = Array.from(document.querySelectorAll('tr.nilai-row'));
        let currentPage = 1;
        let sortOrder = 'desc';

        function makePageItem(label, page, disabled, active, ariaLabel) {
          const li = document.createElement('li');
          li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
          const button = document.createElement('button');
          button.type = 'button';
          button.className = 'page-link';
          button.textContent = label;
          if (ariaLabel) button.setAttribute('aria-label', ariaLabel);
          if (active) button.setAttribute('aria-current', 'page');
          if (disabled) button.disabled = true;
          button.addEventListener('click', function () {
            currentPage = page;
            renderRows();
          });
          li.appendChild(button);
          pagination.appendChild(li);
        }

        function updateSortIndicator() {
          if (sortIcon) {
            sortIcon.className = sortOrder === 'desc'
              ? 'bi bi-arrow-down-short ms-1'
              : 'bi bi-arrow-up-short ms-1';
          }
          if (sortHeader) sortHeader.setAttribute('aria-sort', sortOrder === 'desc' ? 'descending' : 'ascending');
          if (sortButton) {
            sortButton.setAttribute('aria-label', sortOrder === 'desc'
              ? 'Urutkan nilai akhir, saat ini terbesar ke terkecil'
              : 'Urutkan nilai akhir, saat ini terkecil ke terbesar');
          }
        }

        function normalizedPredicate(value) {
          const predicate = (value || '').trim().toLocaleLowerCase('id');
          if (predicate === 'cukup') return 'cukup memuaskan';
          if (predicate === 'kurang') return 'kurang memuaskan';
          return predicate;
        }

        function renderRows() {
          const query = (searchInput ? searchInput.value : '').trim().toLocaleLowerCase('id');
          const filter = predikatFilter ? predikatFilter.value : 'all';
          const pageSizeRaw = pageSizeSelect ? Number.parseInt(pageSizeSelect.value, 10) : 10;
          const pageSize = [5, 10, 15].includes(pageSizeRaw) ? pageSizeRaw : 10;

          const orderedRows = rows.slice().sort(function (a, b) {
            const scoreA = Number.parseFloat(a.dataset.nilai || '0');
            const scoreB = Number.parseFloat(b.dataset.nilai || '0');
            if (scoreA !== scoreB) return sortOrder === 'asc' ? scoreA - scoreB : scoreB - scoreA;
            const nameA = (a.dataset.search || '').toLocaleLowerCase('id');
            const nameB = (b.dataset.search || '').toLocaleLowerCase('id');
            return nameA.localeCompare(nameB, 'id');
          });

          orderedRows.forEach(row => tableBody.insertBefore(row, noMatch || null));
          if (emptyState) tableBody.appendChild(emptyState);
          if (noMatch) tableBody.appendChild(noMatch);

          const filtered = orderedRows.filter(function (row) {
            const nama = (row.dataset.search || '').toLocaleLowerCase('id');
            const predikat = normalizedPredicate(row.dataset.predikat || '');
            return nama.includes(query) && (filter === 'all' || predikat === filter);
          });

          const pages = Math.max(1, Math.ceil(filtered.length / pageSize));
          if (currentPage > pages) currentPage = pages;
          if (currentPage < 1) currentPage = 1;

          rows.forEach(row => row.classList.add('d-none'));
          filtered.slice((currentPage - 1) * pageSize, currentPage * pageSize)
            .forEach(row => row.classList.remove('d-none'));

          if (emptyState) emptyState.classList.toggle('d-none', rows.length > 0);
          if (noMatch) noMatch.classList.toggle('d-none', !(rows.length > 0 && filtered.length === 0));

          if (summary) {
            if (filtered.length === 0) {
              summary.textContent = rows.length === 0
                ? 'Belum ada data nilai'
                : 'Tidak ada data yang cocok dengan pencarian/filter';
            } else {
              const first = (currentPage - 1) * pageSize + 1;
              const last = Math.min(currentPage * pageSize, filtered.length);
              summary.textContent = 'Menampilkan ' + first + '–' + last + ' dari ' + filtered.length + ' data';
            }
          }

          pagination.innerHTML = '';
          const firstDisabled = currentPage === 1 || filtered.length === 0;
          const lastDisabled = currentPage === pages || filtered.length === 0;
          makePageItem('« Pertama', 1, firstDisabled, false, 'Halaman pertama');
          makePageItem('‹ Sebelumnya', Math.max(1, currentPage - 1), firstDisabled, false, 'Halaman sebelumnya');
          for (let page = 1; page <= pages; page++) {
            makePageItem(String(page), page, false, page === currentPage, 'Halaman ' + page);
          }
          makePageItem('Berikutnya ›', Math.min(pages, currentPage + 1), lastDisabled, false, 'Halaman berikutnya');
          makePageItem('Terakhir »', pages, lastDisabled, false, 'Halaman terakhir');
          updateSortIndicator();
        }

        if (searchInput) searchInput.addEventListener('input', function () { currentPage = 1; renderRows(); });
        if (predikatFilter) predikatFilter.addEventListener('change', function () { currentPage = 1; renderRows(); });
        if (pageSizeSelect) pageSizeSelect.addEventListener('change', function () { currentPage = 1; renderRows(); });
        if (sortButton) sortButton.addEventListener('click', function () {
          sortOrder = sortOrder === 'desc' ? 'asc' : 'desc';
          currentPage = 1;
          renderRows();
        });
        renderRows();

        // Isi nilai-nilai yang akan diedit dari atribut data pada tombol di baris tabel.
        const editModal = document.getElementById('modal-edit-user');
        if (editModal) {
          editModal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            if (!trigger) return;
            document.getElementById('edit-nilai-nim-lama').value = trigger.dataset.nim || '';
            document.getElementById('edit-nilai-nim').value = trigger.dataset.nim || '';
            document.getElementById('edit-nilai-nama').value = trigger.dataset.namamhsw || '';
            document.getElementById('edit-nilai-akhir').value = trigger.dataset.nilaiakhir || '';
            document.getElementById('edit-nilai-foto-lama').value = trigger.dataset.foto || '';
            const editFotoInput = document.getElementById('edit-nilai-foto');
            if (editFotoInput) editFotoInput.value = '';
            const editFotoPreview = document.getElementById('edit-nilai-foto-preview');
            if (editFotoPreview) editFotoPreview.src = trigger.dataset.fotoUrl || './assets/img/default-150x150.png';
          });
        }

        // Nilai NIM diterima dari atribut data-nim pada tombol Hapus dan dikirim melalui POST.
        const deleteModal = document.getElementById('modal-delete-user');
        if (deleteModal) {
          deleteModal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            document.getElementById('delete-nilai-nim').value = trigger ? (trigger.dataset.nim || '') : '';
          });
        }

        function bindPhotoPreview(inputId, previewId, fallbackSource) {
          const input = document.getElementById(inputId);
          const preview = document.getElementById(previewId);
          if (!input || !preview) return;
          input.addEventListener('change', function () {
            if (preview.dataset.objectUrl) {
              URL.revokeObjectURL(preview.dataset.objectUrl);
              delete preview.dataset.objectUrl;
            }
            const file = input.files && input.files[0];
            if (!file) { preview.src = fallbackSource(); return; }
            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 2 * 1024 * 1024) {
              input.value = '';
              preview.src = fallbackSource();
              window.alert('Pilih foto JPG, PNG, atau WEBP dengan ukuran maksimal 2 MiB.');
              return;
            }
            const url = URL.createObjectURL(file);
            preview.dataset.objectUrl = url;
            preview.src = url;
          });
        }
        bindPhotoPreview('new-nilai-foto', 'new-nilai-foto-preview', function () {
          return './assets/img/default-150x150.png';
        });
        bindPhotoPreview('edit-nilai-foto', 'edit-nilai-foto-preview', function () {
          const oldFoto = document.getElementById('edit-nilai-foto-lama')?.value || '';
          const demoMap = {
            'demo-user1.jpg': './assets/img/user1-128x128.jpg',
            'demo-user2.jpg': './assets/img/user2-160x160.jpg',
            'demo-user3.jpg': './assets/img/user3-128x128.jpg',
            'demo-user4.jpg': './assets/img/user4-128x128.jpg',
            'demo-user5.jpg': './assets/img/user5-128x128.jpg',
            'demo-user6.jpg': './assets/img/user6-128x128.jpg',
            'demo-user7.jpg': './assets/img/user7-128x128.jpg',
            'demo-user8.jpg': './assets/img/user8-128x128.jpg'
          };
          if (demoMap[oldFoto]) return demoMap[oldFoto];
          return /^[a-f0-9]{32}\\.(jpg|png|webp)$/.test(oldFoto)
            ? './uploads/foto-mahasiswa/' + encodeURIComponent(oldFoto)
            : './assets/img/default-150x150.png';
        });

        // Jika validasi gagal, buka kembali modal dan pulihkan semua value yang dikirim.
        const modalToOpen = <?= json_encode($openModal, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        if (modalToOpen === 'add' || modalToOpen === 'edit') {
          const modalId = modalToOpen === 'add' ? 'modal-add-user' : 'modal-edit-user';
          if (modalToOpen === 'edit') {
            document.getElementById('edit-nilai-nim-lama').value = <?= json_encode((string)($formValues['nim_lama'] ?? ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
            document.getElementById('edit-nilai-nim').value = <?= json_encode((string)($formValues['nim'] ?? ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
            document.getElementById('edit-nilai-nama').value = <?= json_encode((string)($formValues['namamhsw'] ?? ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
            document.getElementById('edit-nilai-akhir').value = <?= json_encode((string)($formValues['nilaiakhir'] ?? ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
            document.getElementById('edit-nilai-foto-lama').value = <?= json_encode((string)($formValues['foto_lama'] ?? ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
            document.getElementById('edit-nilai-foto-preview').src = <?= json_encode(nilaiFotoDisplayUrl((string)($formValues['foto_lama'] ?? '')), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
          }
          const modal = document.getElementById(modalId);
          if (modal && window.bootstrap) bootstrap.Modal.getOrCreateInstance(modal).show();
        }
      });
    </script>
    <!--end::Nilai Mahasiswa interactions-->
  </body>
  <!--end::Body-->
</html>
