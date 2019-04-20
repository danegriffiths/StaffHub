<nav class="navbar navbar-expand-md navbar-light" style="background-color: #00a9a9; font-size=80px">
  <div class="container">
      <div>
          <div class="header-logo" style="float: left; margin-right: 3px">
              <img src="https://assets.publishing.service.gov.uk/static/gov.uk_logotype_crown_invert_trans-203e1db49d3eff430d7dc450ce723c1002542fe1d2bce661b6d8571f14c1043c.png" width="28" height="28" alt="">
          </div>
          <div style="float: left; font-size: 20px">
              <a href="{{ route('dashboard') }}" style="color: inherit">GOV.UK<a>
          </div>
      </div>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">

    <!-- Right Side Of Navbar -->
    <ul class="navbar-nav ml-auto">
      <!-- Authentication Links -->
      @guest
          <li class="nav-item">
              <a class="nav-link" href="{{ route('login') }}"><strong>{{ __('Login') }}</strong></a>
          </li>
      @else
          <li class="nav-item dropdown">
              <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                  <strong>{{ Auth::user()->displayName }}</strong><span class="caret"></span>
              </a>

              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="{{ route('logout') }}"
                     onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();"><strong>
                      {{ __('Logout') }}</strong>
                  </a>

                  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                      @csrf
                  </form>
              </div>
          </li>
      @endguest
    </ul>
  </div>
</div>
</nav>
