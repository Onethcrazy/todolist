<?php

session_start();
// session_destroy(); // ← HAPUS COMMENT ini sekali saja untuk reset data lama

if (!isset($_SESSION['tasks'])) {
  $_SESSION['tasks'] = [];
}

// Tambah tugas baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task']) && !isset($_POST['edit_index'])) {
  $newTask = trim($_POST['task']);
  if ($newTask !== '') {
    $_SESSION['tasks'][] = ['text' => $newTask, 'done' => false];
  }
  header("Location: index.php");
  exit;
}

// Edit tugas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task']) && isset($_POST['edit_index'])) {
  $editIndex = (int) $_POST['edit_index'];
  if (isset($_SESSION['tasks'][$editIndex])) {
    $_SESSION['tasks'][$editIndex]['text'] = trim($_POST['task']);
  }
  header("Location: index.php");
  exit;
}

// Toggle selesai/belum
if (isset($_GET['toggle'])) {
  $index = (int)$_GET['toggle'];
  if (isset($_SESSION['tasks'][$index])) {
    $_SESSION['tasks'][$index]['done'] = !$_SESSION['tasks'][$index]['done'];
  }
  header("Location: index.php");
  exit;
}

// Hapus tugas
if (isset($_GET['delete'])) {
  $index = (int)$_GET['delete'];
  if (isset($_SESSION['tasks'][$index])) {
    array_splice($_SESSION['tasks'], $index, 1);
  }
  header("Location: index.php");
  exit;
}

// Tampilkan form edit jika diminta
$editIndex = isset($_GET['edit']) ? (int) $_GET['edit'] : -1;

// Fungsi tampilkan daftar tugas
function tampilkanDaftar($tasks) {
  global $editIndex;
  foreach ($tasks as $i => $task) {
    $checked = $task['done'] ? 'checked' : '';
    $class = $task['done'] ? 'completed' : '';

    echo "<li class='d-flex align-items-center mb-2 $class'>";

    // Tombol centang
    echo "<form method='get' action='index.php' class='me-2'>";
    echo "<input type='hidden' name='toggle' value='$i'>";
    echo "<input type='checkbox' class='form-check-input' onchange='this.form.submit()' $checked>";
    echo "</form>";

    // Jika sedang diedit
    if ($editIndex === $i) {
      echo "<form method='post' class='d-flex flex-grow-1'>";
      echo "<input type='hidden' name='edit_index' value='$i'>";
      echo "<input type='text' name='task' class='form-control me-2' value='" . htmlspecialchars($task['text']) . "'>";
      echo "<button class='btn btn-success btn-sm me-1' type='submit'>Simpan</button>";
      echo "<a href='index.php' class='btn btn-secondary btn-sm'>Batal</a>";
      echo "</form>";
    } else {
      echo "<span class='flex-grow-1'>" . htmlspecialchars($task['text']) . "</span>";
      echo "<a href='?edit=$i' class='btn btn-warning btn-sm ms-2'>Edit</a>";
      echo "<a href='?delete=$i' class='btn btn-danger btn-sm ms-2'>Hapus</a>";
    }

    echo "</li>";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>To-Do List PHP</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    li.completed span {
      text-decoration: line-through;
      color: gray;
    }
  </style>
</head>
<body class="container py-5">
  <h1 class="mb-4">To-Do List</h1>

  <!-- Form tambah tugas hanya muncul jika tidak sedang edit -->
  <?php if ($editIndex === -1): ?>
    <form class="input-group mb-3" method="POST">
      <input type="text" name="task" class="form-control" placeholder="Tambahkan tugas baru...">
      <button class="btn btn-primary" type="submit">Tambah</button>
    </form>
  <?php endif; ?>

  <ul class="list-unstyled">
    <?php tampilkanDaftar($_SESSION['tasks']); ?>
  </ul>
</body>
</html>
