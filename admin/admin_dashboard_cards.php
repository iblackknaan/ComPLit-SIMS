        <!-- Add this above the cards row -->
        <div class="dashboard-header mb-4">
            <div class="row">
                <div class="col-md-8">
                    <h2><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</h2>
                </div>
                <div class="col-md-4 text-end">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Quick access..." id="dashboardSearch">
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Cards -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <!-- User Management Card -->
            <div class="col">
                <div class="card text-white bg-success h-100">
                    <div class="card-header">
                        <i class="fas fa-user-cog" aria-hidden="true"></i> User Management
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">Manage all users</h2>
                        <p class="card-text">Update, edit, or delete users in the system.</p>
                        <a href="manage_users.php" class="btn btn-light">Manage Users</a>
                    </div>
                </div>
            </div>
            
            <!-- Reports Card -->
            <div class="col">
                <div class="card text-white bg-warning h-100">
                    <div class="card-header">
                        <i class="fas fa-chart-pie" aria-hidden="true"></i> Reports
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">Generate reports</h2>
                        <p class="card-text">View and generate all System reports.</p>
                        <a href="reports.php" class="btn btn-light">Reports</a>
                    </div>
                </div>
            </div>

            <!-- Students List Card -->
            <div class="col">
                <div class="card text-white bg-danger h-100">
                    <div class="card-header">
                        <i class="fas fa-user-graduate" aria-hidden="true"></i> Students List
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">View Students List</h2>
                        <p class="card-text">Show all registered students in the system.</p>
                        <a href="Students_list.php" class="btn btn-light">Students</a>
                    </div>
                </div>
            </div>

            <!-- Add Users Card -->
            <div class="col">
                <div class="card text-white bg-dark h-100">
                    <div class="card-header">
                        <i class="fas fa-user-plus" aria-hidden="true"></i> Add Users
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">Add Users</h2>
                        <p class="card-text">Register new Students, Teachers, and parents to the System</p>
                        <a href="add_user.php" class="btn btn-light">Signup</a>
                    </div>
                </div>
            </div>                        
            
            <!-- Teachers List Card -->
            <div class="col">
                <div class="card text-white bg-primary h-100">
                    <div class="card-header">
                        <i class="fas fa-chalkboard-teacher" aria-hidden="true"></i> Teachers List
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">View Teachers List</h2>
                        <p class="card-text">Show all the Teachers that have been registered to the System</p>
                        <a href="Teachers_list.php" class="btn btn-light">Teachers</a>
                    </div>
                </div>
            </div>
            
            <!-- Parents List Card -->
            <div class="col">
                <div class="card text-white bg-secondary h-100">
                    <div class="card-header">
                        <i class="fas fa-user-tie" aria-hidden="true"></i> Parents List
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">View Parents List</h2>
                        <p class="card-text">Show all the Parents that have been registered to the System</p>
                        <a href="Parents_list.php" class="btn btn-light">Parents</a>
                    </div>
                </div>
            </div>
            
            <!-- Create Classes Card -->
            <div class="col">
                <div class="card text-white bg-info h-100">
                    <div class="card-header">
                        <i class="fas fa-door-open" aria-hidden="true"></i> Create Classes
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">Classes and Class rooms</h2>
                        <p class="card-text">Create the various classes with their corresponding classrooms</p>
                        <a href="classes.php" class="btn btn-light">Classes</a>
                    </div>
                </div>
            </div> 
            
            <!-- Create Subjects Card -->
            <div class="col">
                <div class="card text-white bg-success h-100">
                    <div class="card-header">
                        <i class="fas fa-book" aria-hidden="true"></i> Create Subjects
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">View Subjects</h2>
                        <p class="card-text">Define and manage subjects taught in the school</p>
                        <a href="subjects.php" class="btn btn-light">Subjects</a>
                    </div>
                </div>
            </div>
            
            <!-- Intercom Access Card -->
            <div class="col">
                <div class="card text-white bg-warning h-100">
                    <div class="card-header">
                        <i class="fas fa-comment-dots" aria-hidden="true"></i> Intercom Access
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">View System Intercom</h2>
                        <p class="card-text">Access the system intercom platform to monitor stakeholder communication</p>
                        <a href="admin_intercom_hub.php" class="btn btn-light">Intercom</a>
                    </div>
                </div>
            </div>
            
            <!-- Set Assignments Card -->
            <div class="col">
                <div class="card text-white bg-secondary h-100">
                    <div class="card-header">
                        <i class="fas fa-tasks" aria-hidden="true"></i> Set Assignments
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">Create Assignments</h2>
                        <p class="card-text">Generate the Assignments for the students using the System</p>
                        <a href="Assignments.php" class="btn btn-light">Assignments</a>
                    </div>
                </div>
            </div> 
            
            <!-- Manage Uploads Card -->
            <div class="col">
                <div class="card text-white bg-danger h-100">
                    <div class="card-header">
                        <i class="fas fa-upload" aria-hidden="true"></i> Manage Uploads
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">View Uploads</h2>
                        <p class="card-text">View and manage the Uploads in the System</p>
                        <a href="system_uploads.php" class="btn btn-light">Uploads</a>
                    </div>
                </div>
            </div>
            
            <!-- Settings Card -->
            <div class="col">
                <div class="card text-white bg-primary h-100">
                    <div class="card-header">
                        <i class="fas fa-cogs" aria-hidden="true"></i> Settings
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">System settings</h2>
                        <p class="card-text">Configure system settings and preferences.</p>
                        <a href="settings.php" class="btn btn-light">Settings</a>
                    </div>
                </div>
            </div> 

            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <?php include 'partials/system_status.php'; ?>
                </div>
            </div>



        