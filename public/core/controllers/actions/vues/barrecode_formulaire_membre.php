<div class="form-page">
    <form class="formulaire#CSSALERT#" method="post" action="#ACTIONFORM#" id="finish">
        <h2>#TITREACTIONS#</h2>
        <div title="Ici Renseignez le Code Barre !" class="ligne">
            <i class="fas fa-barcode"></i>
            <input autofocus type="text" id="codebarre" class="email shadows" name="codebarre" placeholder="Codebarre" value="#CODEBARRE#"
            onfocus="this.placeholder = ''"
            onblur="this.placeholder = 'Codebarre'">
        </div>
        <div class="ligne submit" title="Connectez-vous !" >
            <button>submit</button>
            <i class="far fa-check-circle"></i>
            <!-- <i class="fas fa-check-circle"></i> -->
        </div>
        <input type="hidden" id="actiontype" name="action" value="#ACTIONTYPE#">
    </form>
</div>
#MESSAGE#