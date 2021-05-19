<div class="listeactions">
	<div class="pagesize">
		<div class="pagesection">
			<div class="section">
				<h1>ReaderSql</h1>
					<!-- <img title="IMC BARRECODE" alt="logo et lien vers la page accueil" src="theme/img/logo.png"><br/> -->
					<div class="importnav">
						<ul class="import">
							#NAVITEMS#
						</ul>
					</div>
					<div id="sqlexport" class="sqlexport">
						<div id="sqlrequest" class="sqlrequest">
							<button id="refreshsourcesql">refresh</button>
							<textarea id="sqlstring" name="sqlstring" rows="5">#SQL#</textarea>
							<p>1 - <input type="checkbox" id="skeepid" name="skeepid"> Ignorer les id ? <span id="skeepspan">(champ à ignorer si coché #SDBNbLines#<input type="text" id="skeepname" name="skeepname">)</span></p>
							<p>1 - <input type="checkbox" id="wherein" name="wherein"> wherein ? <span id="whereinspan">( #SDBNbLines#<input type="text" id="wherename" name="wherename">)</span></p>
						</div>
					</div>
					<div id="importexport" class="importexport">
						<div id="sourcedb" class="sourcedb">
							<p>Source : #SDBName#.#STBName#</p>
							<p>nbChamps : #SDBNbChamps#</p>
							<p>nomsChamps : #SDBNbLines#</p>
							#SDBCONTENTS#
						</div>
						<div id="destindb" class="destindb">
							<p>Destin. : #DDBName#.#DTBName#</p>
							<p>nbChamps : #DDBNbChamps#</p>
							<p>nomsChamps : #DDBNbLines#</p>
							#DDBCONTENTS#
						</div>
					</div>
					#CONTENTS#
				</div>
			</div>
		</div>
	<div class="sectionfooter">.IMPORT.EXPORT.</div>
</div>