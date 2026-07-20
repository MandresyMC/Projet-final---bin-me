<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>

    <?php if (session()->getFlashdata('error')) { ?>
        <p style="color: red;"><?= session()->getFlashdata('error') ?></p>
    <?php } ?>

    <form action="<?= base_url('/login') ?>" method="post">
        <label for="numero_telephone">Numéro de téléphone: <b>+261</b></label>
        <input
            type="text"
            id="numero_telephone"
            name="numero_telephone"
            maxlength="9"
            placeholder="33 12 345 67"
            required
        >
        <br><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>