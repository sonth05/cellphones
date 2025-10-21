<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/Database.php';

// Simple router using `page` query param
$page = $_GET['page'] ?? 'home';

function view(string $name, array $data = []): void {
	extract($data);
	require __DIR__ . '/../views/layout/header.php';
	require __DIR__ . '/../views/' . $name . '.php';
	require __DIR__ . '/../views/layout/footer.php';
}

switch ($page) {
	case 'home':
		view('home', ['title' => 'CellphoneS - Trang chủ']);
		break;
	case 'mirror':
		$cloneIndex = __DIR__ . '/clone_cellphones/index.html';
		if (file_exists($cloneIndex)) {
			header('Content-Type: text/html; charset=UTF-8');
			echo file_get_contents($cloneIndex);
		} else {
			http_response_code(404);
			echo 'Cloned UI not deployed. Run scripts\\deploy_mirror.php';
		}
		break;
	default:
		http_response_code(404);
		view('404', ['title' => 'Không tìm thấy']);
}

