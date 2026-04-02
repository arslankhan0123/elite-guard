<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }

        .register-container {
            background-color: #fff;
            padding: 50px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .register-container h2 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
            color: #333;
        }

        .form-control {
            height: 45px;
            border-radius: 8px;
            margin-bottom: 15px;
            padding: 10px;
        }

        .register-btn {
            background-color: #28a745;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-weight: bold;
            color: #fff;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .register-btn:hover {
            background-color: #218838;
        }

        .input-group-text {
            background-color: transparent;
            border: none;
            cursor: pointer;
        }

        a {
            color: #28a745;
            text-decoration: none;
            font-size: 0.9rem;
        }

        a:hover {
            text-decoration: underline;
        }

        .btn-facebook {
            background-color: #3b5998;
            color: white;
            font-weight: 600;
        }

        .btn-facebook:hover {
            background-color: #2d4373;
            color: white;
        }

        .btn-google {
            background-color: #db4437;
            color: white;
            font-weight: 600;
        }

        .btn-google:hover {
            background-color: #c23321;
            color: white;
        }

        .btn-linkedin {
            background-color: #0077b5;
            color: white;
            font-weight: 600;
        }

        .btn-linkedin:hover {
            background-color: #005582;
            color: white;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h2>Create Your Account</h2>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <input type="text" name="name" class="form-control" placeholder="Full Name" required>
            <x-input-error :messages="$errors->get('name')" style="color: red;" class="mt-2" />

            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
            <x-input-error :messages="$errors->get('email')" style="color: red;" class="mt-2" />

            <div class="input-group mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
                <button type="button" style="background: #e8f0fe; height:45px" class="input-group-text"><i class='bx bx-hide'></i></button>
            </div>
            <x-input-error :messages="$errors->get('password')" style="color: red;" class="mt-2" />

            <div class="input-group mb-3">
                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                <button type="button" style="background: #e8f0fe; height:45px" class="input-group-text"><i class='bx bx-hide'></i></button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" style="color: red;" class="mt-2" />

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="termsCheck" required>
                <label class="form-check-label" for="termsCheck">I agree to the Terms & Conditions</label>
            </div>

            <button type="submit" class="btn register-btn">Sign Up</button>
            <p style="text-align: center; font-weight:700">OR</p>
            <!-- <div class="text-center">
                <a href="" class="btn btn-facebook w-100 mb-2">
                    <i class='bx bxl-facebook me-2'></i> Sign in with Facebook
                </a>
                <a href="" class="btn btn-google w-100 mb-2">
                    <i class='bx bxl-google me-2'></i> Sign in with Google
                </a>
                <a href="" class="btn btn-linkedin w-100">
                    <i class='bx bxl-linkedin-square me-2'></i> Sign in with LinkedIn
                </a>
            </div> -->
            <p class="mt-3">Already have an account? <a href="{{ route('login') }}">Sign In</a></p>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $('.input-group-text').on('click', function() {
                let input = $(this).siblings('input');
                let icon = $(this).find('i');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('bx-hide').addClass('bx-show');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('bx-show').addClass('bx-hide');
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>