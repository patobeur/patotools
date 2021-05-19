<div class="form-page">
    <form class="formulaire#CSSALERT#" method="post" action="#ACTIONFORM#" id="finish">
        <h2><i class="fas fa-laptop"></i> <i class="fas fa-barcode"></i> <i class="fas fa-equals"></i> <i class="fas fa-laptop"></i> <i class="far fa-calendar-minus"></i></h2>
        <h2>#TITREACTIONS#</h2>
        <div title="Ici Renseignez le Code Barre !" class="ligne">
            <i class="fas fa-barcode"></i><i class="fas deuz fa-laptop"></i>
            <input autofocus type="text" id="codebarre" class="email shadows" name="codebarre" placeholder="Codebarre" value="#CODEBARRE#"
            onfocus="this.placeholder = ''"
            onblur="this.placeholder = 'Codebarre'">
        </div>
        <div class="ligne submit" title="Envoyer le formulaire !" >
            <button>submit</button>
            <i class="far fa-check-circle"></i>
            <!-- <i class="fas fa-check-circle"></i> -->
        </div>
        <input type="hidden" id="actiontype" name="action" value="#ACTIONTYPE#">
    </form>
</div>
#MESSAGE#