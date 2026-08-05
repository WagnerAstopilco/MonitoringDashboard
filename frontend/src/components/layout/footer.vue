<template>
    <footer class="bg-dark text-light mt-auto">
        <div class="container py-3">
            <div class="row">
                <div class="col-md-3 mb-3">
                    
                    <img src="@/assets/images/logo.png" alt="Logo Gato Negro" class="footer-logo mb-3" style="max-width:150px; height: auto; border-radius: 50%;" />
                </div>

                <!-- Nosotros -->

                <div class="col-md-3 mb-3">
                    <h5>Nosotros</h5>

                    <p>
                        Somos una agencia de publicidad enfocada en soluciones creativas y digitales para
                        empresas.
                    </p>
                </div>

                <!-- Servicios -->

                <div class="col-md-3 mb-3">
                    <h5>Servicios</h5>

                    <ul class="list-unstyled">
                        <li>Diseño gráfico</li>
                        <li>Branding</li>
                        <li>Publicidad digital</li>
                        <li>Impresión</li>
                    </ul>
                </div>

                <!-- Contacto -->

                <div class="col-md-3 mb-3">
                    <h5>Contacto</h5>

                    <ul class="list-unstyled">
                        <li>📞 +51 952 635 357</li>

                        <li>✉ contacto@gatonegro.com</li>

                        <li><i class="bi bi-facebook"></i> <a href="https://web.facebook.com/profile.php?id=61566568613517&locale=es_LA" target="_blank" class="text-light text-decoration-none">Facebook</a></li>

                        <li><i class="bi bi-instagram"></i> <a href="https://www.instagram.com/blackcatpublicidad/" target="_blank" class="text-light text-decoration-none">Instagram</a></li>
                        <li><i class="bi bi-tiktok"></i> <a href="https://www.tiktok.com/@blackcatpublicidad" target="_blank" class="text-light text-decoration-none">Tik tok</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="text-center border-top py-3">
            © {{ new Date().getFullYear() }}
            Gato Negro. Todos los derechos reservados.
        </div>
    </footer>
</template>
