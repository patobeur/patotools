<form class="formcommentaires" method="post" action="#ACTIONFORM#" id="finish">
		<h2>#TITREH2#</h2>
		<textarea id="commentaireretard" name="commentaire" class="comments shadows" placeholder="Votre commentaire ici ..." onfocus="this.placeholder = ''" onblur="this.placeholder = 'Votre commentaire ici ...'"></textarea>
		<input type="hidden" name="currentform" value="#HIDDENACTION#">
		<div class="coment submit" title="Envoyez votre commentaire !" >
				<button>#SUBMITCONTENT#</button>
				<i class="far fa-check-circle"></i>
				<!-- <i class="fas fa-check-circle"></i> -->
		</div>
    <input type="hidden" id="actiontype" name="action" value="#HIDDENACTIONTYPE#">
</form>