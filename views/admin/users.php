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
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="/user/edit/<?= $user->id; ?>" class="btn btn-warning">Modifier</a>
                                </div>
                                <div class="col-md-6">
                                    <form action="/admin/deleteUser" method="POST" onsubmit="return confirm('Êtes-vous vraiment sûr de vouloir supprimer ce poste ?')">
                                        <input type="hidden" name="token" value="<?= $token; ?>">
                                        <input type="hidden" name="id" value="<?= $user->id; ?>">
                                        <button type="submit" class="btn btn-danger">Supprimer</a>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>