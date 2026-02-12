function togglePasswordVisibility() {
    // Referências aos elementos
    const senhaInput = document.getElementById("senha");
    const toggleIcon = document.getElementById("toggle-icon");
    
    // Caminhos das imagens (ajuste se os nomes dos arquivos forem diferentes)
    const iconOpenPath = 'img/visivel.png';
    const iconClosedPath = 'img/invisivel.png';
    
    // Alterna o tipo do input e a imagem do ícone
    if (senhaInput.type === "password") {
        senhaInput.type = "text";
        // Altera para o ícone de 'olho fechado'
        toggleIcon.src = toggleIcon.src.replace(iconOpenPath, iconClosedPath);
        toggleIcon.alt = "Ocultar Senha";
    } else {
        senhaInput.type = "password";
        // Altera para o ícone de 'olho aberto'
        toggleIcon.src = toggleIcon.src.replace(iconClosedPath, iconOpenPath);
        toggleIcon.alt = "Mostrar Senha";
    }
}


document.addEventListener('DOMContentLoaded', function(){
    // busca todas as mensagens com a classe que eu defini no HTML
    const messages = document.querySelectorAll('.flash-message');

    messages.forEach(function(message){
        // define o tempo que ela fica visível
        setTimeout(function(){
            message.classList.add('fade-out');
        
            setTimeout(function(){
                message.remove();
            }, 300); // tempo do fade-out
        }, 3000);
    });
// });


    const observer = new IntersectionObserver((entries) => {
        
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('ativo');
            }
            // Se quiser que a animação aconteça toda vez que subir/descer, 
            // descomente a linha abaixo:
            // else { entry.target.classList.remove('ativo'); }
        });
    }, {
        threshold: 0.1 // Ativa quando 10% do elemento aparece
    });

    // Seleciona todos os elementos que têm a classe 'revelar'
    const elementosParaRevelar = document.querySelectorAll('.revelar');
    elementosParaRevelar.forEach((el) => observer.observe(el));
});