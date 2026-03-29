<?php
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-file-export"></i> Export
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-sync"></i> Refresh
                </button>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                <i class="fas fa-calendar-alt"></i> This week
            </button>
        </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-file-alt me-2"></i>Posts
                </div>
                <div class="card-body">
                    <h2 class="card-title">24</h2>
                    <p class="card-text">Total Posts</p>
                    <div class="progress">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 75%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-users me-2"></i>Users
                </div>
                <div class="card-body">
                    <h2 class="card-title">12</h2>
                    <p class="card-text">Total Users</p>
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 45%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-comments me-2"></i>Comments
                </div>
                <div class="card-body">
                    <h2 class="card-title">48</h2>
                    <p class="card-text">Pending Comments</p>
                    <div class="progress">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 90%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-history me-2"></i>Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>John Doe</td>
                                    <td>Created new post</td>
                                    <td>2 hours ago</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
                                <tr>
                                    <td>Jane Smith</td>
                                    <td>Updated settings</td>
                                    <td>5 hours ago</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                </tr>
                                <tr>
                                    <td>Admin</td>
                                    <td>Added new user</td>
                                    <td>1 day ago</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
                                <tr>
                                    <td>Mike Johnson</td>
                                    <td>Deleted post</td>
                                    <td>2 days ago</td>
                                    <td><span class="badge bg-danger">Failed</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>