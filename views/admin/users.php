<section>
    <div class="container mt-4">
        <h1>Page admin user</h1>
        <table class="table table-hover table-bordered mt-4">
            <thead>
                <th>Id</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Rôles</th>
                <th>Actions</th>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?= $user->id; ?></td>
                        <td><?= $user->nom; ?></td>
                        <td><?= $user->prenom; ?></td>
                        <td><?= $user->email; ?></td>
                        <td><?= $user->roles; ?></td>
                        <td>
                            <a href="/user/edit/<?= $user->id; ?>" class="btn btn-warning">Modifier</a>
                            <a href="/admin/deleteUser/<?= $user->id; ?>" class="btn btn-danger">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>