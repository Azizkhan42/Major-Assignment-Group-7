<?php
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/utils.php';
startSession();
?>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white navbar-shadow">
    <div class="navbar-container">
        <!-- Navbar Brand -->
        <a class="navbar-brand" href="/Personal-budget-dashboard/modules/dashboard.php">
            <i class="fas fa-wallet"></i>
            <span class="brand-text">Budget Dashboard</span>
        </a>

        <!-- Navbar Toggler for Mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarContent">
            <!-- Left side - Empty space for flexibility -->
            <ul class="navbar-nav ms-auto">
            </ul>

            <!-- Right side - User Profile -->
            <div class="navbar-user ms-auto">
                <span class="welcome-text">
                    <i class="fas fa-hand-wave"></i> Welcome, 
                    <strong><?php echo htmlspecialchars(substr($_SESSION['user_name'] ?? 'User', 0, 20)); ?></strong>
                </span>

                <!-- Profile Dropdown -->
                <div class="profile-dropdown">
                    <!-- Profile Image Circle (Clickable for upload) -->
                    <div class="profile-image-container" title="Click to upload profile picture">
                        <label for="profile_pic_input" class="profile-image-label">
                            <?php
                            $initials = getUserInitials($_SESSION['user_name'] ?? 'User');
                            $profile_pic = $_SESSION['user_profile_picture'] ?? null;
                            ?>
                            <?php if ($profile_pic && file_exists(__DIR__ . '/../assets/uploads/profile_pictures/' . $profile_pic)): ?>
                                <img src="/Personal-budget-dashboard/assets/uploads/profile_pictures/<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile" class="profile-image-pic">
                            <?php else: ?>
                                <div class="profile-image">
                                    <span class="profile-initials"><?php echo htmlspecialchars($initials); ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="profile-upload-overlay">
                                <i class="fas fa-camera"></i>
                            </div>
                        </label>
                        <input type="file" id="profile_pic_input" class="d-none" accept="image/*" onchange="uploadProfilePicture(this)">
                    </div>

                    <!-- Dropdown Menu -->
                    <div class="dropdown-menu-profile">
                        <div class="dropdown-header">
                            <div class="user-info-dropdown">
                                <div class="user-avatar-large">
                                    <?php if ($profile_pic && file_exists(__DIR__ . '/../assets/uploads/profile_pictures/' . $profile_pic)): ?>
                                        <img src="/Personal-budget-dashboard/assets/uploads/profile_pictures/<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile" class="profile-avatar-pic">
                                    <?php else: ?>
                                        <span class="profile-initials-large"><?php echo htmlspecialchars($initials); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="user-details">
                                    <p class="user-name-dropdown"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                                    <p class="user-email-dropdown"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                                </div>
                            </div>
                        </div>

                        <hr class="dropdown-divider">

                        <a href="/Personal-budget-dashboard/modules/profile.php" class="dropdown-item">
                            <i class="fas fa-user-circle"></i>
                            <span>My Profile</span>
                        </a>

                        <a href="/Personal-budget-dashboard/modules/dashboard.php" class="dropdown-item">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>

                        <a href="/Personal-budget-dashboard/modules/transactions.php" class="dropdown-item">
                            <i class="fas fa-exchange-alt"></i>
                            <span>Transactions</span>
                        </a>

                        <hr class="dropdown-divider">

                        <a href="/Personal-budget-dashboard/logout.php" class="dropdown-item logout-item">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
    .navbar {
        border-bottom: 1px solid #e5e7eb;
        position: relative;
        z-index: 100;
    }

    .navbar-shadow {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .navbar-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 30px;
        width: 100%;
    }

    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        color: #667eea;
        font-weight: 700;
        font-size: 20px;
        margin: 0;
    }

    .brand-text {
        display: inline-block;
    }

    .navbar-user {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-left: auto;
    }

    .welcome-text {
        font-size: 14px;
        color: #4b5563;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .welcome-text strong {
        color: #667eea;
    }

    /* Profile Image Circle */
    .profile-image-container {
        position: relative;
        cursor: pointer;
    }

    .profile-image-label {
        display: flex;
        cursor: pointer;
        position: relative;
    }

    .profile-image {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.25);
        position: relative;
    }

    .profile-image:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .profile-image-pic {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.25);
    }

    .profile-image-pic:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .profile-upload-overlay {
        position: absolute;
        bottom: -5px;
        right: -5px;
        width: 22px;
        height: 22px;
        background: #667eea;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .profile-image-label:hover .profile-upload-overlay {
        opacity: 1;
    }

    .profile-initials {
        color: white;
        font-weight: 700;
        font-size: 16px;
    }

    /* Dropdown Menu */
    .profile-dropdown {
        position: relative;
    }

    .dropdown-menu-profile {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        min-width: 280px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        margin-top: 15px;
        z-index: 1000;
    }

    .profile-dropdown:hover .dropdown-menu-profile {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .profile-image-container:hover .dropdown-menu-profile {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-header {
        padding: 20px;
    }

    .user-info-dropdown {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .user-avatar-large {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
    }

    .profile-avatar-pic {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-initials-large {
        color: white;
        font-weight: 700;
        font-size: 18px;
    }

    .user-details {
        flex: 1;
        min-width: 0;
    }

    .user-name-dropdown {
        margin: 0;
        font-weight: 600;
        color: #1f2937;
        font-size: 14px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-email-dropdown {
        margin: 3px 0 0 0;
        font-size: 12px;
        color: #6b7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dropdown-divider {
        margin: 8px 0;
        border-top: 1px solid #f3f4f6;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        color: #4b5563;
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 14px;
    }

    .dropdown-item:hover {
        background-color: #f9fafb;
        color: #667eea;
        padding-left: 25px;
    }

    .dropdown-item i {
        width: 16px;
        text-align: center;
    }

    .dropdown-item.logout-item {
        color: #ef4444;
    }

    .dropdown-item.logout-item:hover {
        background-color: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .navbar-container {
            padding: 10px 20px;
        }

        .welcome-text {
            display: none;
        }

        .brand-text {
            display: none;
        }

        .navbar-brand {
            font-size: 24px;
        }

        .profile-image {
            width: 40px;
            height: 40px;
        }

        .profile-initials {
            font-size: 14px;
        }

        .dropdown-menu-profile {
            right: -50px;
        }
    }
</style>

<script>
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const profileDropdown = document.querySelector('.profile-dropdown');
        if (profileDropdown && !profileDropdown.contains(event.target)) {
            // Dropdown will auto close due to CSS :hover
        }
    });

    // Upload profile picture
    function uploadProfilePicture(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                return;
            }
            
            const formData = new FormData();
            formData.append('profile_picture', file);
            
            // Show loading state
            const label = document.querySelector('.profile-image-label');
            const originalContent = label.innerHTML;
            label.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            fetch('/Personal-budget-dashboard/upload_profile_picture.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to show new profile picture
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                    label.innerHTML = originalContent;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Upload failed');
                label.innerHTML = originalContent;
            });
            
            // Reset input
            input.value = '';
        }
    }
</script>
