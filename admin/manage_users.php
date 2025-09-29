<?php 
session_start();
include_once __DIR__ . '/../includes/db.php';
require_once '../classes/User.php';

$db = new Database();
$pdo = $db->pdo;

// Si un utilisateur soumet le formulaire pour créer un nouvel utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $user = new User(null, $username, $password, $role);
    $user->create($pdo);
    $_SESSION['message'] = 'Utilisateur créé avec succès !';
    $_SESSION['messageClass'] = 'bg-green-500';
    header('Location: manage_users.php');
    exit();
}

// Si un utilisateur veut supprimer un utilisateur
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    User::delete($pdo, $id);
    $_SESSION['message'] = 'Utilisateur supprimé avec succès !';
    $_SESSION['messageClass'] = 'bg-red-500';
    header('Location: manage_users.php');
    exit();
}

// Si un utilisateur veut modifier un utilisateur
if (isset($_GET['edit_id'])) {
    $id = $_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Si un utilisateur soumet le formulaire pour mettre à jour l'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $user = new User($id, $username, $password, $role);
    $user->update($pdo);
    $_SESSION['message'] = 'Utilisateur mis à jour avec succès !';
    $_SESSION['messageClass'] = 'bg-blue-500';
    header('Location: manage_users.php');
    exit();
}

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
        }
        .wrapper {
            display: flex;
            height: 100vh;
            background-color: #f4f5f7;
        }
        .sidebar {
            width: 250px;
            background-color: #1f2937;
            color: #fff;
            padding-top: 30px;
        }
        .content {
            flex-grow: 1;
            padding: 40px;
            overflow-y: auto;
        }
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: #fff;
            padding: 30px;
            margin: 10% auto;
            width: 50%;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .modal-header {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2d3748;
        }
        .modal-footer button {
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .modal-footer button:hover {
            background-color: #edf2f7;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="wrapper">
        <div class="sidebar">
            <?php include_once __DIR__ . '/../includes/sidebar.php'; ?>
        </div>

        <div class="content">
            <div class="container mx-auto">
                <h1 class="text-4xl font-extrabold text-gray-800 mb-8">Gestion des utilisateurs</h1>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="p-4 text-white <?php echo $_SESSION['messageClass']; ?> mb-6 rounded-lg shadow-md">
                        <?php echo $_SESSION['message']; ?>
                    </div>
                    <?php 
                        unset($_SESSION['message']);
                        unset($_SESSION['messageClass']);
                    ?>
                <?php endif; ?>

                <!-- Formulaire de création d'utilisateur -->
                <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
                    <h4 class="text-xl font-semibold text-gray-700 mb-4">Ajouter un utilisateur</h4>
                    <form method="POST">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-600">Nom d'utilisateur</label>
                                <input type="text" name="username" id="username" class="w-full p-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-600">Mot de passe</label>
                                <input type="password" name="password" id="password" class="w-full p-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-600">Rôle</label>
                                <select name="role" id="role" class="w-full p-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="user">Utilisateur</option>
                                    <option value="admin">Administrateur</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" name="create" class="w-full mt-6 p-4 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition duration-300">Créer l'utilisateur</button>
                    </form>
                </div>

                <!-- Liste des utilisateurs -->
                <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
                    <h4 class="text-xl font-semibold text-gray-700 mb-6">Liste des utilisateurs</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto text-sm text-gray-700">
                            <thead>
                                <tr class="border-b">
                                    <th class="px-6 py-3 text-left">#</th>
                                    <th class="px-6 py-3 text-left">Nom d'utilisateur</th>
                                    <th class="px-6 py-3 text-left">Rôle</th>
                                    <th class="px-6 py-3 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 1; ?>
                                <?php foreach ($users as $user): ?>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-6 py-3"><?php echo $counter++; ?></td>
                                        <td class="px-6 py-3"><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td class="px-6 py-3"><?php echo htmlspecialchars($user['role']); ?></td>
                                        <td class="px-6 py-3 flex space-x-4">
                                            <!-- Bouton Modifier -->
                                            <button onclick="openModal(<?php echo $user['id']; ?>, '<?php echo addslashes(htmlspecialchars($user['username'])); ?>', '<?php echo $user['password']; ?>', '<?php echo $user['role']; ?>')" class="p-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition duration-300">Modifier</button>
    
                                            <!-- Bouton Supprimer -->
                                            <a href="manage_users.php?delete_id=<?php echo $user['id']; ?>" class="p-2 bg-red-500 text-white rounded hover:bg-red-600 transition duration-300" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour la modification de l'utilisateur -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header text-center mb-6">
                <h4 class="font-semibold text-xl">Modifier l'utilisateur</h4>
            </div>
            <form id="editForm" method="POST">
                <input type="hidden" name="id" id="editId">
                <div class="mb-4">
                    <label for="editUsername" class="block text-sm font-medium text-gray-600">Nom d'utilisateur</label>
                    <input type="text" name="username" id="editUsername" class="w-full p-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="editPassword" class="block text-sm font-medium text-gray-600">Mot de passe</label>
                    <input type="password" name="password" id="editPassword" class="w-full p-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-6">
                    <label for="editRole" class="block text-sm font-medium text-gray-600">Rôle</label>
                    <select name="role" id="editRole" class="w-full p-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="user">Utilisateur</option>
                        <option value="admin">Administrateur</option>
                    </select>
                </div>
                <div class="modal-footer text-center">
                    <button type="button" onclick="closeModal()" class="p-2 bg-gray-300 text-white rounded hover:bg-gray-400">Annuler</button>
                    <button type="submit" name="edit" class="p-2 bg-blue-500 text-white rounded hover:bg-blue-600">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Ouvrir le modal avec les données de l'utilisateur
        function openModal(id, username, password, role) {
            document.getElementById('editId').value = id;
            document.getElementById('editUsername').value = username;
            document.getElementById('editPassword').value = password;
            document.getElementById('editRole').value = role;
            document.getElementById('editModal').style.display = 'block';
        }

        // Fermer le modal
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Fermer le modal si l'utilisateur clique en dehors du contenu du modal
        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>
