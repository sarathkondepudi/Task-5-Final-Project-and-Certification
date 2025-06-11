<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

function requireRole($roles) {
    if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], (array)$roles)) {
        ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
        body {
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(-45deg, #ee7752,rgb(247, 31, 247),rgb(14, 181, 241), #23d5ab);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            overflow: hidden;
        }
        @keyframes gradientBG {
            0% {background-position: 0% 50%;}
            50% {background-position: 100% 50%;}
            100% {background-position: 0% 50%;}
        }
        </style>
        <div class="modal fade show" id="accessDeniedModal" tabindex="-1" aria-labelledby="accessDeniedLabel" style="display:block; background:rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="accessDeniedLabel">Access Denied</h5>
              </div>
              <div class="modal-body text-center">
                <p>You do not have permission to access this page.</p>
                <a href="../auth/login.php" class="btn btn-primary">Go to Login</a>
              </div>
            </div>
          </div>
        </div>
        <script>
          document.body.style.overflow = 'hidden';
        </script>
        <?php
        exit;
    }
}
?>