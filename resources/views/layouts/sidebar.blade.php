<div class="sidebar">
  <nav class="sidebar-nav">
      <ul class="nav">
          <li class="nav-item">
              <a class="nav-link" href="/">
                  <i class="nav-icon icon-speedometer"></i> Dashboard
              </a>
          </li>
          <li class="nav-item">
              <a class="nav-link" href="/permohonan_ska">
                  <i class="nav-icon icon-user"></i> Permohonan SKA
              </a>
          </li>
          <li class="nav-item">
              <a class="nav-link" href="/permohonan_skt">
                  <i class="nav-icon icon-user"></i> Permohonan SKT
              </a>
          </li>
          @if(Helpers::checkPermission('verify') )
            <li class="nav-title">Kirim VVA</li>
            <li class="nav-item">
                <a class="nav-link" href="/pengajuan_naik_status/ska">
                    <i class="nav-icon icon-user"></i> SKA
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/pengajuan_naik_status/skt">
                    <i class="nav-icon icon-user"></i> SKT
                </a>
            </li>
          @endif

        @if(Helpers::checkPermission('user') || Helpers::checkPermission('role') )
            <li class="nav-title">Settings</li>
            @if(Helpers::checkPermission('user'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('users') }}">
                        <i class="nav-icon icon-user"></i> Users
                    </a>
                </li>
            @endif
            @if(Helpers::checkPermission('role') )
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('user_role') }}">
                        <i class="nav-icon icon-lock"></i> Roles
                    </a>
                </li>
            @endif
        @endif
      </ul>
  </nav>
  <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>