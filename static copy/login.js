

const formLogin = document.getElementById('form-login');

if (formLogin) {

  formLogin.addEventListener('submit', function(e) {

    e.preventDefault();

    const feedback = document.getElementById('feedback');
    const senha = document.getElementById('senha').value;

    if (abaAtiva === 'empresa') {

      const cnpj = document.getElementById('cnpj').value.trim();

      if (!cnpj || !senha) {

        feedback.className = 'form-feedback erro';
        feedback.textContent =
        '⚠️ Preencha o CNPJ e a senha.';

        return;
      }

      if (cnpj.replace(/\D/g, '').length !== 14) {

        feedback.className = 'form-feedback erro';
        feedback.textContent = '⚠️ CNPJ inválido.';

        return;
      }

    }

    if (abaAtiva === 'motorista') {

      const cpf = document.getElementById('cpf').value.trim();

      if (!cpf || !senha) {

        feedback.className = 'form-feedback erro';
        feedback.textContent =
        '⚠️ Preencha o CPF e a senha.';

        return;
      }

      if (cpf.replace(/\D/g, '').length !== 11) {

        feedback.className = 'form-feedback erro';
        feedback.textContent = '⚠️ CPF inválido.';

        return;
      }

    }

    feedback.className = 'form-feedback sucesso';

    feedback.textContent =
    '✅ Login realizado!';

    setTimeout(function() {

      window.location.href = 'dashboard.html';

    }, 1500);

  });

}