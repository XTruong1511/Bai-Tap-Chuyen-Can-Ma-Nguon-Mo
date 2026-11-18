<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\Branch;

class BranchController
{
    private Branch $branchModel;

    public function __construct()
    {
        Auth::requireLogin();
        $this->branchModel = new Branch();
    }

    public function index(): void
    {
        Auth::requireAdmin();
        $branches = $this->branchModel->all();
        require_once __DIR__ . '/../../views/branches/index.php';
    }

    public function create(): void
    {
        Auth::requireAdmin();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $branchName = trim($_POST['branch_name'] ?? '');
            $address = trim($_POST['address'] ?? '');

            if (empty($branchName)) $errors[] = 'Vui lòng nhập tên cơ sở.';
            if (empty($address)) $errors[] = 'Vui lòng nhập địa chỉ cơ sở.';

            if (empty($errors)) {
                $this->branchModel->create([
                    'branch_name' => $branchName,
                    'address' => $address
                ]);
                Auth::setFlash('success', "Thêm cơ sở {$branchName} thành công!");
                header('Location: index.php?controller=branch&action=index');
                exit;
            }
        }

        require_once __DIR__ . '/../../views/branches/create.php';
    }

    public function edit(): void
    {
        Auth::requireAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        $branch = $this->branchModel->find($id);

        if (!$branch) {
            Auth::setFlash('danger', 'Không tìm thấy cơ sở.');
            header('Location: index.php?controller=branch&action=index');
            exit;
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $branchName = trim($_POST['branch_name'] ?? '');
            $address = trim($_POST['address'] ?? '');

            if (empty($branchName)) $errors[] = 'Vui lòng nhập tên cơ sở.';
            if (empty($address)) $errors[] = 'Vui lòng nhập địa chỉ cơ sở.';

            if (empty($errors)) {
                $this->branchModel->update($id, [
                    'branch_name' => $branchName,
                    'address' => $address
                ]);
                Auth::setFlash('success', "Cập nhật cơ sở {$branchName} thành công!");
                header('Location: index.php?controller=branch&action=index');
                exit;
            }
        }

        require_once __DIR__ . '/../../views/branches/edit.php';
    }

    public function delete(): void
    {
        Auth::requireAdmin();
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id) {
            $this->branchModel->delete($id);
            Auth::setFlash('success', 'Đã xóa cơ sở thành công.');
        }
        header('Location: index.php?controller=branch&action=index');
        exit;
    }
}
