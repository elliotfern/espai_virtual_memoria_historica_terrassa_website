<?php

echo '
	<div class="text-end">';

// Genera o obtén tu clave secreta
$loggedInUser = ($_SESSION['user']['id']);
?>
<script type="module">
	import { nameUser } from "<?php echo APP_DEV; ?>/public/js/auth/auth.js";
	nameUser('<?php echo $loggedInUser; ?>')
</script>

<?php

	echo '<div id="userDiv"> </div>';
	echo ' | <a href="'.APP_DEV.'/logout">(Logout)</a>
	</div>

	<div class="container-fluid text-center" style="padding-top:20px;padding-bottom:25px">
		<h1>Espai Virtual de la Memòria Històrica de Terrassa - EVMHT</h1>
		<h4 class="text-center"><a href="'. APP_DEV .'/admin">Gestió Interna</a></h4>
	</div>
	
	<div class="container-fluid text-center">
		<nav class="navbar navbar-expand-lg bg-body-tertiary text-center" style="display:block;margin-top:10px;margin-bottom:35px">
			<button class="navbar-toggler text-center" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon text-center"></span>
			</button>
			<p><span class="text-center"><a href="'. APP_DEV .'/represaliats">Llistat complert</a></span></p>

		<div class="collapse navbar-collapse justify-content-center menuHeader" id="navbarTogglerDemo01">
		<ul class="navbar-nav text-center">
				<li class="nav-item nav-link">
					<a href="'.APP_DEV.'/afusellats">01. Afusellats</a>
				</li>

				<li class="nav-item nav-link">
					<a href="'.APP_DEV.'/deportats">02. Deportats</a>
				</li>

				<li class="nav-item nav-link">
					<a href="'.APP_DEV.'/exiliats">03. Exiliats</a>
				</li>
				<li class="nav-item nav-link">
					<a href="'.APP_DEV.'/cost-huma">04. Cost humà</a>
				</li>
				<li class="nav-item nav-link">
					<a href="'.APP_DEV.'/represaliats">05. Represaliats</a>
				</li>	
			</ul>
		</nav>
	</div>';