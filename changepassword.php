<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <form action="changepassword_process.php" method="post" class="bg-white shadow-md rounded-lg px-8 pt-6 pb-8 mb-4">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Đổi mật khẩu</h2>
            
            <?php if (isset($_GET['error'])) { ?>
                <p class="text-red-500 text-sm text-center mb-4"><?php echo htmlspecialchars($_GET['error']); ?></p>
            <?php } ?>

            <?php if (isset($_GET['success'])) { ?>
                <p class="text-green-500 text-sm text-center mb-4"><?php echo htmlspecialchars($_GET['success']); ?></p>
            <?php } ?>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="old_password">
                    Mật khẩu cũ
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       id="old_password" type="password" name="old_password" placeholder="Enter your old password" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="new_password">
                    Mật khẩu mới
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       id="new_password" type="password" name="new_password" placeholder="Enter your new password" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="confirm_new_password">
                    Nhập lại mật khẩu mới
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       id="confirm_new_password" type="password" name="confirm_new_password" placeholder="Confirm your new password" required>
            </div>

            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                        type="submit">
                    Xác nhận
                </button>
                <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="index.php">
                    Về trang chủ
                </a>
            </div>
        </form>
    </div>
    <script>
        // Add this script to show/hide password
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>