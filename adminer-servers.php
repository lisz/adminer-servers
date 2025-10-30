<?php
// driver: server=MySQL | pgsql=PostgreSQL
$servers = [];
$adminerUrl = 'http://127.0.0.1/adminer/adminer-zh.php';

$localFile = __DIR__ . '/.adminer-servers.json';
if (file_exists($localFile)) {
  $localConfig = json_decode(file_get_contents($localFile), true);
  if (is_array($localConfig)) {
    if (isset($localConfig['servers']) && is_array($localConfig['servers'])) {
      $servers = array_merge($servers, $localConfig['servers']);
    }
    if (isset($localConfig['adminerUrl']) && is_string($localConfig['adminerUrl'])) {
      $adminerUrl = $localConfig['adminerUrl'];
    }
  }
}

$groupOptions = array_values(array_filter(array_unique(array_column($servers, 'group'))));
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>adminer 快捷登录</title>
  <style>
    :root {
      --primary-color: #3498db;
      --secondary-color: #2980b9;
      --success-color: #2ecc71;
      --danger-color: #e74c3c;
      --light-color: #ecf0f1;
      --dark-color: #34495e;
      --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: #f5f7fa;
      color: #333;
      line-height: 1.6;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    header {
      background-color: white;
      box-shadow: var(--shadow);
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    h1 {
      color: var(--dark-color);
      font-size: 24px;
    }

    .adminer-link {
      background-color: var(--primary-color);
      color: white;
      padding: 10px 20px;
      border-radius: 4px;
      text-decoration: none;
      font-weight: 500;
      transition: background-color 0.3s;
    }

    .adminer-link:hover {
      background-color: var(--secondary-color);
    }

    .search-container {
      margin-bottom: 20px;
      display: flex;
      gap: 10px;
    }

    .search-input {
      flex: 1;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 16px;
    }

    .filter-select {
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 4px;
      background-color: white;
      font-size: 16px;
    }

    .servers-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 20px;
    }

    .server-card {
      position: relative;
      background-color: white;
      border-radius: 8px;
      box-shadow: var(--shadow);
      padding: 20px;
      transition: transform 0.3s, box-shadow 0.3s;
      cursor: pointer;
    }

    .server-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .server-name {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 10px;
      color: var(--dark-color);
    }

    .server-type {
      font-size: 14px;
      font-weight: 600;
      color: #2597f4ff;
      position: absolute;
      top: 10px;
      right: 10px;
    }

    .server-details {
      display: flex;
      flex-direction: column;
      gap: 8px;
      margin-bottom: 15px;
    }

    .server-detail {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .server-detail i {
      color: var(--primary-color);
      width: 20px;
      font-style: normal;
    }

    .login-btn {
      background-color: var(--success-color);
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 4px;
      cursor: pointer;
      font-weight: 500;
      width: 100%;
      transition: background-color 0.3s;
    }

    .login-btn:hover {
      background-color: #27ae60;
    }

    .status-indicator {
      display: inline-block;
      width: 10px;
      height: 10px;
      border-radius: 50%;
      margin-right: 5px;
    }

    .status-online {
      background-color: var(--success-color);
    }

    .status-offline {
      background-color: var(--danger-color);
    }

    .no-results {
      grid-column: 1 / -1;
      text-align: center;
      padding: 40px;
      color: #7f8c8d;
    }

    footer {
      text-align: center;
      margin-top: 40px;
      color: #7f8c8d;
      font-size: 14px;
    }

    @media (max-width: 768px) {
      .servers-grid {
        grid-template-columns: 1fr;
      }

      header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <header>
      <h1>adminer 快捷登录</h1>
    </header>

    <div class="search-container">
      <input type="text" class="search-input" placeholder="搜索数据库服务器..." id="searchInput">
      <select class="filter-select" id="groupFilter">
        <option value="all">所有分组</option>
        <?php foreach ($groupOptions as $groupName): ?>
          <option value="<?= $groupName ?>"><?= $groupName ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="servers-grid" id="serversGrid">
      <!-- 服务器卡片将通过JavaScript动态生成 -->
    </div>

    <footer>
      <p>数据库快捷登录页面 &copy; Lis</p>
    </footer>
  </div>

  <script>
    // 模拟从PHP配置中读取的服务器列表
    const servers = <?= empty($servers) ? '[]' : json_encode($servers, 384) ?>;
    const driverMap = {
      server: {
        label: 'MySQL',
        color: '#2ACF4F'
      },
      pgsql: {
        label: 'PostgreSQL',
        color: '#037BF8'
      },
      sqlite: {
        label: 'SQLite',
        color: '#42D7CB'
      },
      sqlsrv: {
        label: 'SQL Server',
        color: '#FF9E2B'
      },
      oracle: {
        label: 'Oracle',
        color: '#FF0030'
      }
    };

    // 渲染服务器卡片
    function renderServers(serversToRender) {
      const serversGrid = document.getElementById('serversGrid');

      if (serversToRender.length === 0) {
        serversGrid.innerHTML = '<div class="no-results">未找到匹配的数据库服务器</div>';
        return;
      }

      serversGrid.innerHTML = serversToRender.map(server => {
        const dbType = driverMap[server.driver] ? driverMap[server.driver] : {
          label: server.driver,
          color: ''
        };

        return `
<div class="server-card" data-id="${server.id}">
  <div class="server-name">${server.name}</div>
  <div class="server-type" style="color: ${dbType.color}">${dbType.label}</div>
  <div class="server-details">
    <div class="server-detail">
      <i>🔗</i>
      <span>${server.host}</span>
    </div>
    <div class="server-detail">
      <i>👨‍💻</i>
      <span>${server.username}</span>
    </div>
    <div class="server-detail" style="display: ${server.database ? 'flex' : 'none'};">
      <i>📒</i>
      <span>${server.database}</span>
    </div>
    <div class="server-detail">
      <i>🌐</i>
      <span>${server.group}</span>
    </div>
  </div>
  <form method="post" action="<?= $adminerUrl ?>" target="_blank" class="login-form">
    <input type="hidden" name="auth[driver]" value="${server.driver}">
    <input type="hidden" name="auth[server]" value="${server.host}">
    <input type="hidden" name="auth[username]" value="${server.username}">
    <input type="hidden" name="auth[password]" value="${server.password}">
    <input type="hidden" name="auth[db]" value="${server.database}">
    <input type="hidden" name="auth[permanent]" value="1">
    <button type="submit" class="login-btn">登录到数据库</button>
  </form>
</div>
            `
      }).join('');
    }

    // 初始渲染
    renderServers(servers);

    // 搜索和过滤功能
    document.getElementById('searchInput').addEventListener('input', filterServers);
    document.getElementById('groupFilter').addEventListener('change', filterServers);

    function filterServers() {
      const searchTerm = document.getElementById('searchInput').value.toLowerCase();
      const groupFilter = document.getElementById('groupFilter').value;

      const filteredServers = servers.filter(server => {
        const matchesSearch = server.name.toLowerCase().includes(searchTerm) ||
          server.host.toLowerCase().includes(searchTerm) ||
          server.database.toLowerCase().includes(searchTerm);

        const matchesGroup = groupFilter === 'all' || server.group === groupFilter;

        return matchesSearch && matchesGroup;
      });

      renderServers(filteredServers);
    }
  </script>
</body>

</html>