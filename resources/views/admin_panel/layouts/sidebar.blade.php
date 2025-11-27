<aside
    class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 my-2 ps bg-gradient-dark bg-white"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer opacity-5 position-absolute end-0 top-0 d-none d-xl-none text-white"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand px-4 py-3 m-0" href="" target="_blank">
            <img src="{{ asset('admin_panel/assets/img/logo-ct.png') }}" class="navbar-brand-img" width="26"
                height="26" alt="main_logo">
            <span class="ms-1 text-sm text-white">MriduuKriti</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto ps ps--active-y" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link text-white {{ Request::is('dashboard*') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('dashboard') }}">
                    <i class="material-symbols-rounded opacity-5">dashboard</i>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ Request::is('category*') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('category.index') }}">
                    <i class="material-symbols-rounded opacity-5">category</i>
                    <span class="nav-link-text ms-1">Category</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ Request::is('sub_category*') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('sub_category.index') }}">
                    <i class="material-symbols-rounded opacity-5">list</i>
                    <span class="nav-link-text ms-1">Sub Category</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ Request::is('attribute*') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('attribute.index') }}">
                    <i class="material-symbols-rounded opacity-5">tune</i>
                    <span class="nav-link-text ms-1">Attribute</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white {{ Request::is('products*') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('products.index') }}">
                    <i class="material-symbols-rounded opacity-5">inventory_2</i>

                    <span class="nav-link-text ms-1">Products</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ Request::is('banners*') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('banners.index') }}">
                    <i class="material-symbols-rounded opacity-5">image</i>
                    <span class="nav-link-text ms-1">Banner</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ Request::is('orders*') ? 'active bg-gradient-primary' : '' }}" href="{{ route('orders.index') }}">
                    <i class="material-symbols-rounded opacity-5">orders</i>
                    <span class="nav-link-text ms-1">Order</span>
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link text-white " href="{{ route('banners.index') }}">
                    <i class="material-symbols-rounded opacity-5">local_shipping</i>
                    <span class="nav-link-text ms-1">Shipping</span>
                </a>
            </li> --}}


            <li class="nav-item">
                <a class="nav-link text-white {{ Request::is('payment-transactions*') ? 'active bg-gradient-primary' : '' }}" href="{{ route('payment-transactions.index') }}">
                    <i class="material-symbols-rounded opacity-5">payment</i>
                    <span class="nav-link-text ms-1">Payment Transaction</span>
                </a>
            </li>
              <li class="nav-item">
                <a class="nav-link text-white {{ Request::is('subscribers*') ? 'active bg-gradient-primary' : '' }}" href="{{ route('subscribers.index') }}">
                    <i class="material-symbols-rounded opacity-5">mail</i>
                    <span class="nav-link-text ms-1">Subscribers</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ Request::is('users*') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('users.index') }}">
                    <i class="material-symbols-rounded opacity-5">people</i>
                    <span class="nav-link-text ms-1">User Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ Request::is('setting*') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('setting.index') }}">
                    <i class="material-symbols-rounded opacity-5">settings</i>
                    <span class="nav-link-text ms-1">Setting</span>
                </a>
            </li>



        </ul>
        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
        </div>
        <div class="ps__rail-y" style="top: 0px; height: 386px; right: 0px;">
            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 383px;"></div>
        </div>
    </div>
    <div class="sidenav-footer position-absolute w-100 bottom-0 ">
        <div class="mx-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a class="btn bg-gradient-dark w-100" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                                        this.closest('form').submit();"
                    type="button"><i class="material-symbols-rounded opacity-5">login</i> Log Out</a>
            </form>
        </div>
    </div>
    <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
        <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
    </div>
    <div class="ps__rail-y" style="top: 0px; right: 0px;">
        <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
    </div>
</aside>
