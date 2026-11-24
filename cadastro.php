<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="mb-3">Criar Conta</h3>
                <form action="actions/auth_cadastro.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nome completo</label>
                        <input type="text" name="nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Senha</label>
                        <input type="password" name="senha" class="form-control" minlength="4" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Cadastrar</button>
                </form>
                <p class="mt-3 mb-0 text-center small">
                    Já tem conta? <a href="login.php">Entrar</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>