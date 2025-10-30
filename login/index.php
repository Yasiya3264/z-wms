<?php
session_start();
$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Zynex WMS | Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="../inc/global.css">

  <style>
    section {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
      box-sizing: border-box;
    }

    .login-card {
      max-width: 340px;
      width: 100%;
      padding: 20px;
    }

    .login-card p {
      color: var(--color-text-secondary);
      font-size: 14px;
      text-align: center;
      margin-top: 0;
      margin-bottom: 20px;
    }

    .message.alert-danger {
      margin-bottom: 15px;
      font-size: 14px;
      font-weight: 500;
    }

    .login-card h1 {
      text-align: center;
      font-size: 24px;
      margin-bottom: 5px;
      color: var(--color-accent-blue);
    }

    .login-card .action-button {
      width: 100%;
      margin-top: 15px;
    }
  </style>

</head>

<body>

  <section>
    <div class="card login-card">
      <form action="login.php" method="post">
        <h1>Z-WMS</h1>
        <p>Advanced warehouse management system</p>

        <?php if ($error): ?>
          <div class="message alert-danger">
            <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <div class="input-group">
          <label for="username">User Name</label>
          <input type="text" name="username" id="username" required autofocus>
        </div>

        <div class="input-group">
          <label for="warehouse_code">Warehouse Code</label>
          <select name="wh_code" id="warehouse_code" required>
            <option value="">-- Select Warehouse Code --</option>
          </select>
        </div>

        <div class="input-group">
          <label for="compcode">Company Code</label>
          <select name="compcode" id="compcode" required>
            <option value="">-- Select Company Code --</option>
          </select>
        </div>

        <div class="input-group">
          <label for="password">Password</label>
          <input type="password" name="password" id="password" required>
        </div>

        <button type="submit" name="login_btn" class="action-button">Login</button>
        <footer style="margin-top: 2vh; font-size: 0.5rem;">
          <p>Designed and Developed by <b>Zynex Solutions</b> 2019-2025&copy;</p>
        </footer>
      </form>
    </div>
  </section>

  <script>
    // --- JavaScript Logic for fetching data, now with robust exponential backoff ---
    const usernameInput = document.getElementById('username');
    const warehouseCodeSelect = document.getElementById('warehouse_code');
    const compcodeSelect = document.getElementById('compcode');

    // Function to handle fetching with exponential backoff
    async function fetchDataWithRetry(url, body) {
      const maxRetries = 3;
      for (let i = 0; i < maxRetries; i++) {
        try {
          const response = await fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: body
          });

          if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

          return await response.json();
        } catch (error) {
          if (i === maxRetries - 1) {
            console.error(`Failed to fetch from ${url} after multiple retries:`, error);
            throw error; // Re-throw the error after all retries fail
          }
          const delay = Math.pow(2, i) * 1000; // Exponential backoff: 1s, 2s, 4s
          await new Promise(resolve => setTimeout(resolve, delay));
        }
      }
    }


    // Fetch warehouse codes on username input
    usernameInput.addEventListener('keyup', async () => {
      const username = usernameInput.value.trim();

      // Clear company codes immediately
      compcodeSelect.innerHTML = '<option value="">-- Select Company Code --</option>';

      if (username.length > 0) {
        warehouseCodeSelect.innerHTML = '<option value="">-- Fetching... --</option>';

        try {
          const body = `username=${encodeURIComponent(username)}`;
          const data = await fetchDataWithRetry('fetch_data.php', body);

          // Clear and populate warehouse codes
          warehouseCodeSelect.innerHTML = '<option value="">-- Select Warehouse Code --</option>';
          if (data && data.wh_codes && data.wh_codes.length > 0) {
            data.wh_codes.forEach(wh_code => {
              const option = document.createElement('option');
              option.value = wh_code;
              option.textContent = wh_code;
              warehouseCodeSelect.appendChild(option);
            });
          } else {
            warehouseCodeSelect.innerHTML = '<option value="">-- No codes found --</option>';
          }
        } catch (error) {
          warehouseCodeSelect.innerHTML = '<option value="">-- Failed to load --</option>';
        }
      } else {
        warehouseCodeSelect.innerHTML = '<option value="">-- Select Warehouse Code --</option>';
      }
    });

    // Fetch company codes when a warehouse is selected
    warehouseCodeSelect.addEventListener('change', async () => {
      const username = usernameInput.value.trim();
      const wh_code = warehouseCodeSelect.value;

      compcodeSelect.innerHTML = '<option value="">-- Fetching... --</option>';

      if (username.length > 0 && wh_code.length > 0) {
        try {
          const body = `username=${encodeURIComponent(username)}&wh_code=${encodeURIComponent(wh_code)}`;
          const data = await fetchDataWithRetry('fetch_data_2.php', body);

          // Clear and populate company codes
          compcodeSelect.innerHTML = '<option value="">-- Select Company Code --</option>';
          if (data && data.compcodes && data.compcodes.length > 0) {
            data.compcodes.forEach(compcode => {
              const option = document.createElement('option');
              option.value = compcode;
              option.textContent = compcode;
              compcodeSelect.appendChild(option);
            });
          } else {
            compcodeSelect.innerHTML = '<option value="">-- No codes found --</option>';
          }
        } catch (error) {
          compcodeSelect.innerHTML = '<option value="">-- Failed to load --</option>';
        }
      } else {
        // If no username or warehouse code is selected, reset to default state
        compcodeSelect.innerHTML = '<option value="">-- Select Company Code --</option>';
      }
    });
  </script>

</body>

</html>
