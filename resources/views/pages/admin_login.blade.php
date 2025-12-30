<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UnK STORE Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
</head>

<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="login-container">
            <h2 class="text-center mb-4" style="font-weight: bold;">ĐĂNG NHẬP ADMIN</h2>
            <?php
                $message = Session::get('message');
                if($message){
                    echo '<span class-a="text-alert">'.$message.'</span>';
                    Session::put('message',null);
                }
            ?>
            <form action="{{ URL::to('/admin-dashboard') }}" method="post">
            {{ csrf_field() }}
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

               

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" class="form-control" id="email" 
                           value="<?php if (isset($_POST['email'])) echo $email ?>"
                           maxlength="30"
                           required>
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu:</label>
                    <input type="password" name="password" class="form-control" id="password" 
                           value="<?php if (isset($_POST['password'])) echo $password ?>"
                           maxlength="30"
                           required>
                </div>

                <button type="submit" class="btn btn-block">Đăng Nhập</button>
            </form>
            <p class="text-center mt-3">Tài khoản admin không thể đăng kí. Vui lòng liên hệ quản trị viên...</p>
        </div>
    </div>
</body>

<style>
        body {
            background-image: url('https://revolutionwatch.com/wp-content/uploads/2023/04/01-Art-of-Watch-Collecting-2048x1365.jpg'); /* Thay đường dẫn này bằng ảnh nền thực tế */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            color: #fff;
        }

        .login-container {
            max-width: 400px;
            background: rgba(255, 255, 255, 0.82);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
            color:black;
            font-weight: bold;
        }

        .form-control {
            background: rgb(255, 255, 255);
            border: none;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #007bff;
        }

        .btn-block {
            background-color:rgba(32, 63, 214, 0.62);
            color: white;
            display: block;
            margin: 0 auto;
            width: 200px;
        }

        .btn-block:hover {
            background-color:rgba(255, 0, 0, 0.78);
            color:white;
        }

        .alert {
            background: rgba(255, 0, 0, 0.8);
            color: white;
        }
    </style>
</html>