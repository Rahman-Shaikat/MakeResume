@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<header class="admin-page-heading">
    <div>
        <span class="admin-eyebrow">Platform overview</span>
        <h1>Dashboard</h1>
        <p>Monitor Resume Studio activity, adoption, and platform health.</p>
    </div>
    <div class="admin-heading-actions">
        @if (auth('admin')->user()->hasPermission('admin-users-create'))
            <a href="{{ route('admin.admin-users.create') }}" class="btn btn-primary">
                @include('admin.components.icon', ['name' => 'user-plus'])
                Add administrator
            </a>
        @endif
    </div>
</header>

<section class="admin-stat-grid" aria-label="Platform statistics">
    <article class="admin-stat-card is-primary">
        <span class="admin-stat-icon">@include('admin.components.icon', ['name' => 'users'])</span>
        <div>
            <span>Total users</span>
            <strong>{{ number_format($totalUsers) }}</strong>
            <small><b>Registered</b> user accounts</small>
        </div>
        <span class="admin-stat-chart" aria-hidden="true">
            <i style="height: 34%"></i><i style="height: 48%"></i><i style="height: 42%"></i><i style="height: 65%"></i><i style="height: 78%"></i><i style="height: 92%"></i>
        </span>
    </article>
    <article class="admin-stat-card is-success">
        <span class="admin-stat-icon">@include('admin.components.icon', ['name' => 'resume'])</span>
        <div>
            <span>Resumes created</span>
            <strong>{{ number_format($totalResumes) }}</strong>
            <small><b>Saved</b> resume records</small>
        </div>
        <span class="admin-stat-chart" aria-hidden="true">
            <i style="height: 45%"></i><i style="height: 39%"></i><i style="height: 58%"></i><i style="height: 71%"></i><i style="height: 68%"></i><i style="height: 96%"></i>
        </span>
    </article>
    <article class="admin-stat-card is-info">
        <span class="admin-stat-icon">@include('admin.components.icon', ['name' => 'layout'])</span>
        <div>
            <span>Administrators</span>
            <strong>{{ number_format($activeAdministrators) }}</strong>
            <small><b>Active</b> admin accounts</small>
        </div>
        <span class="admin-stat-chart" aria-hidden="true">
            <i style="height: 55%"></i><i style="height: 55%"></i><i style="height: 68%"></i><i style="height: 68%"></i><i style="height: 84%"></i><i style="height: 84%"></i>
        </span>
    </article>
    <article class="admin-stat-card is-warning">
        <span class="admin-stat-icon">@include('admin.components.icon', ['name' => 'document'])</span>
        <div>
            <span>Access roles</span>
            <strong>{{ number_format($activeRoles) }}</strong>
            <small><b>Active</b> administrator roles</small>
        </div>
        <span class="admin-stat-chart" aria-hidden="true">
            <i style="height: 35%"></i><i style="height: 50%"></i><i style="height: 62%"></i><i style="height: 57%"></i><i style="height: 75%"></i><i style="height: 88%"></i>
        </span>
    </article>
</section>

<section class="admin-summary-grid" aria-label="Monthly summary">
    <article class="admin-summary-card">
        <div>
            <span>Verified users</span>
            <strong>10,863</strong>
        </div>
        <span class="admin-summary-icon text-primary">@include('admin.components.icon', ['name' => 'shield'])</span>
        <footer><span><b class="is-positive">+9.8%</b> vs last month</span><a href="#">View users</a></footer>
    </article>
    <article class="admin-summary-card">
        <div>
            <span>New resumes this month</span>
            <strong>2,416</strong>
        </div>
        <span class="admin-summary-icon text-success">@include('admin.components.icon', ['name' => 'trend'])</span>
        <footer><span><b class="is-positive">+21.3%</b> vs last month</span><a href="#">View resumes</a></footer>
    </article>
    <article class="admin-summary-card">
        <div>
            <span>Open support requests</span>
            <strong>18</strong>
        </div>
        <span class="admin-summary-icon text-warning">@include('admin.components.icon', ['name' => 'help'])</span>
        <footer><span><b class="is-negative">5 urgent</b> require attention</span><a href="#">Open inbox</a></footer>
    </article>
</section>

<section class="row g-3 mb-3">
    <div class="col-xl-7">
        <article class="admin-panel h-100">
            <header class="admin-panel-heading">
                <div>
                    <span>Platform growth</span>
                    <h2>Resume creation overview</h2>
                </div>
                <select class="form-select form-select-sm" aria-label="Chart period">
                    <option>This year</option>
                    <option>This month</option>
                    <option>This week</option>
                </select>
            </header>
            <div class="admin-panel-body">
                <div class="admin-chart-summary">
                    <div><strong>28,906</strong><span>Total resumes</span></div>
                    <div><i class="is-orange"></i><span>Created</span></div>
                    <div><i class="is-soft"></i><span>Published</span></div>
                </div>
                <div class="admin-bar-chart" aria-label="Static chart showing resume creation growth from January through December">
                    <div class="admin-chart-axis"><span>4k</span><span>3k</span><span>2k</span><span>1k</span><span>0</span></div>
                    <div class="admin-chart-grid">
                        @foreach ([
                            ['Jan', 38, 28], ['Feb', 47, 35], ['Mar', 44, 32], ['Apr', 56, 41],
                            ['May', 63, 47], ['Jun', 58, 43], ['Jul', 70, 52], ['Aug', 66, 49],
                            ['Sep', 77, 58], ['Oct', 82, 62], ['Nov', 88, 66], ['Dec', 96, 74],
                        ] as [$month, $created, $published])
                            <div class="admin-chart-column">
                                <div><i style="height: {{ $created }}%"></i><b style="height: {{ $published }}%"></b></div>
                                <span>{{ $month }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </article>
    </div>

    <div class="col-xl-5">
        <article class="admin-panel h-100">
            <header class="admin-panel-heading">
                <div>
                    <span>User health</span>
                    <h2>Account overview</h2>
                </div>
                <button type="button" class="admin-more-button" aria-label="More options">@include('admin.components.icon', ['name' => 'more'])</button>
            </header>
            <div class="admin-account-overview">
                <div class="admin-donut" aria-label="87 percent of accounts are verified">
                    <div><strong>87%</strong><span>Verified</span></div>
                </div>
                <div class="admin-account-legend">
                    <div><i class="is-success"></i><span>Verified users</span><strong>10,863</strong><small>87%</small></div>
                    <div><i class="is-warning"></i><span>Pending verification</span><strong>1,204</strong><small>10%</small></div>
                    <div><i class="is-muted"></i><span>Inactive accounts</span><strong>413</strong><small>3%</small></div>
                </div>
            </div>
            <div class="admin-account-stats">
                <div><strong>1,284</strong><span>New this month</span></div>
                <div><strong>4,896</strong><span>Active this week</span></div>
                <div><strong>98.7%</strong><span>Good standing</span></div>
            </div>
        </article>
    </div>
</section>

<section class="row g-3">
    <div class="col-xl-4">
        <article class="admin-panel h-100">
            <header class="admin-panel-heading">
                <div><span>Performance</span><h2>Popular templates</h2></div>
                <button type="button" class="btn btn-light btn-sm">This month</button>
            </header>
            <div class="admin-list">
                @foreach ([
                    ['Professional Cyan', '4,821 resumes', '34%', 'cyan'],
                    ['Classic Blue Sidebar', '3,906 resumes', '28%', 'blue'],
                    ['Modern Mint Professional', '2,774 resumes', '19%', 'mint'],
                    ['Structured Indigo', '1,946 resumes', '13%', 'indigo'],
                    ['Teal Impact', '982 resumes', '6%', 'teal'],
                ] as [$name, $usage, $share, $theme])
                    <a href="#" class="admin-template-row">
                        <span class="admin-template-thumb is-{{ $theme }}"><i></i><b></b></span>
                        <span><strong>{{ $name }}</strong><small>{{ $usage }}</small></span>
                        <em>{{ $share }}</em>
                    </a>
                @endforeach
            </div>
        </article>
    </div>

    <div class="col-xl-4">
        <article class="admin-panel h-100">
            <header class="admin-panel-heading">
                <div><span>Community</span><h2>Recent users</h2></div>
                <a href="#">View all</a>
            </header>
            <div class="admin-list">
                @foreach ([
                    ['avatar-2.jpg', 'Nadia Rahman', 'nadia@example.com', 'Verified'],
                    ['avatar-3.jpg', 'Daniel Foster', 'daniel@example.com', 'Verified'],
                    ['avatar-4.jpg', 'Samira Khan', 'samira@example.com', 'Pending'],
                    ['avatar-5.jpg', 'James Wilson', 'james@example.com', 'Verified'],
                    ['avatar-6.jpg', 'Priya Sharma', 'priya@example.com', 'Pending'],
                ] as [$avatar, $name, $email, $status])
                    <a href="#" class="admin-user-row">
                        <img src="{{ asset('assets/admin/images/avatars/'.$avatar) }}" alt="">
                        <span><strong>{{ $name }}</strong><small>{{ $email }}</small></span>
                        <em class="{{ $status === 'Verified' ? 'is-verified' : 'is-pending' }}">{{ $status }}</em>
                    </a>
                @endforeach
            </div>
        </article>
    </div>

    <div class="col-xl-4">
        <article class="admin-panel h-100">
            <header class="admin-panel-heading">
                <div><span>Live feed</span><h2>Recent activity</h2></div>
                <a href="#">Activity log</a>
            </header>
            <div class="admin-activity-list">
                <div>
                    <span class="admin-event-icon is-success">@include('admin.components.icon', ['name' => 'user-plus'])</span>
                    <p><strong>New account created</strong><span>Nadia Rahman joined Resume Studio.</span><small>8 minutes ago</small></p>
                </div>
                <div>
                    <span class="admin-event-icon is-primary">@include('admin.components.icon', ['name' => 'resume'])</span>
                    <p><strong>Resume completed</strong><span>A resume was exported using Professional Cyan.</span><small>24 minutes ago</small></p>
                </div>
                <div>
                    <span class="admin-event-icon is-warning">@include('admin.components.icon', ['name' => 'settings'])</span>
                    <p><strong>Template updated</strong><span>Modern Mint preview styles were refreshed.</span><small>1 hour ago</small></p>
                </div>
                <div>
                    <span class="admin-event-icon is-info">@include('admin.components.icon', ['name' => 'shield'])</span>
                    <p><strong>Administrator invited</strong><span>A new content administrator was invited.</span><small>3 hours ago</small></p>
                </div>
            </div>
        </article>
    </div>
</section>
@endsection
