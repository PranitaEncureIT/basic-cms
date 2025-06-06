<style>
  .navbar-toggler i,
.topbar-toggler i,
.nav-toggle i {
    color: black !important;
}

</style>
<div class="main-header">
<!-- Logo Header -->
<div class="logo-header" data-background-color="light2">
  @php
    $logoPath = public_path('assets/front/img/' . $bs->logo);
  @endphp
  <a href="{{ route('front.index') }}" class="logo" target="_blank">
    @if (file_exists($logoPath) && !empty($bs->logo))
      <img src="{{ asset('assets/front/img/' . $bs->logo) }}" alt="Navbar Brand" class="navbar-brand" width="60">
    @else
      <img src="{{ asset('assets/front/img/logo2.png') }}" alt="Default Logo" class="navbar-brand" width="60">
    @endif
  </a>

  <!-- Hamburger (Closes Sidebar) -->
  <button class="navbar-toggler sidenav-toggler ml-auto text-dark" type="button">
    <span class="navbar-toggler-icon">
      <i class="icon-menu"></i>
    </span>
  </button>

  <!--  (Opens Sidebar) -->
  <button class="topbar-toggler more text-dark">
    <i class="icon-options-vertical"></i>
  </button>

  <div class="nav-toggle">
    <button class="btn btn-toggle text-dark @if(request()->routeIs('admin.file-manager')) sidenav-overlay-toggler @else toggle-sidebar @endif">
      <i class="icon-menu"></i>
    </button>
  </div>
</div>

  <!-- End Logo Header -->

  <!-- Navbar Header -->
  <nav class="navbar navbar-header navbar-expand-lg" data-background-color="light">

    <div class="container-fluid">

      <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">

        <li class="nav-item dropdown hidden-caret">
          <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
            <div class="avatar-sm">
              @if (!empty(Auth::guard('admin')->user()->image))
                <img src="{{asset('assets/admin/img/propics/'.Auth::guard('admin')->user()->image)}}" alt="..." class="avatar-img rounded-circle">
              @else
                <img src="{{asset('assets/admin/img/propics/blank_user.jpg')}}" alt="..." class="avatar-img rounded-circle">
              @endif
            </div>
          </a>
          <ul class="dropdown-menu dropdown-user animated fadeIn">
            <div class="dropdown-user-scroll scrollbar-outer">
              <li>
                <div class="user-box">
                  <div class="avatar-lg">
                    @if (!empty(Auth::guard('admin')->user()->image))
                      <img src="{{asset('assets/admin/img/propics/'.Auth::guard('admin')->user()->image)}}" alt="..." class="avatar-img rounded">
                    @else
                      <img src="{{asset('assets/admin/img/propics/blank_user.jpg')}}" alt="..." class="avatar-img rounded">
                    @endif
                  </div>
                  <div class="u-text">
                    <h4>{{Auth::guard('admin')->user()->first_name}}</h4>
                    <p class="text-muted">{{Auth::guard('admin')->user()->email}}</p>
                  </div>
                </div>
              </li>
              <li>
                <div class="dropdown-divider"></div>
                @can('profile')
                <a class="dropdown-item" href="{{route('admin.editProfile')}}">Edit Profile</a>
                @endcan
                <div class="dropdown-divider"></div>
                @can('change-password')
                <a class="dropdown-item" href="{{route('admin.changePass')}}">Change Password</a>
                @endcan
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{route('admin.logout')}}">Logout</a>
              </li>
            </div>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
  <!-- End Navbar -->
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    let sidebar = document.querySelector(".sidebar"); // Change this selector if needed

    // Hamburger Button (Closes Sidebar)
    document.querySelector(".navbar-toggler").addEventListener("click", function () {
        sidebar.classList.toggle("active"); // Add or remove 'active' class
    });

    // Three Dots Button (Opens Sidebar)
    document.querySelector(".topbar-toggler").addEventListener("click", function () {
        sidebar.classList.add("active"); // Force open the sidebar
    });
});

</script>
