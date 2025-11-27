<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>



<!-- Tailwind + Icons -->

<script src="https://cdn.tailwindcss.com"></script>

<script>

    tailwind.config = {

        darkMode: 'class'

    }

</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>



<!-- Toggle Dark Mode -->

<script>

    function toggleDarkMode() {

        document.documentElement.classList.toggle('dark');

        localStorage.setItem("theme", document.documentElement.classList.contains("dark") ? "dark" : "light");

    }



    // Mantener tema

    document.addEventListener("DOMContentLoaded", function () {

        if (localStorage.getItem("theme") === "dark") {

            document.documentElement.classList.add("dark");

        }

    });

</script>



<div class="px-6 py-6 bg-gray-50 dark:bg-gray-900 min-h-screen transition-all">



    <!-- ENCABEZADO -->

    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">



        <div>

            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">

                <i class="fa-solid fa-user-circle text-blue-600"></i>

                Detalle del Usuario

            </h1>



            <!-- Breadcrumb -->

            <nav class="mt-2 text-sm">

                <ol class="flex items-center text-gray-500 dark:text-gray-300 gap-2">

                    <li><a href="<?= site_url('admin/dashboard') ?>" class="hover:text-blue-600">Inicio</a></li>

                    <span>/</span>

                    <li><a href="<?= site_url('admin/usuarios') ?>" class="hover:text-blue-600">Usuarios</a></li>

                    <span>/</span>

                    <li class="text-gray-700 dark:text-white font-semibold">

                        <?= $usuario->nombre . ' ' . $usuario->apellido ?>

                    </li>

                </ol>

            </nav>

        </div>



        <div class="flex items-center gap-3">

            <button onclick="toggleDarkMode()"

                class="px-3 py-1.5 rounded-lg bg-gray-200 dark:bg-gray-700 dark:text-white">

                <i class="fa-solid fa-moon"></i> / <i class="fa-solid fa-sun"></i>

            </button>



            <a href="<?= site_url('admin/usuarios') ?>"

                class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:text-white">

                <i class="fa-solid fa-arrow-left mr-1"></i>Volver

            </a>

        </div>

    </div>





    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">



        <!-- PERFIL -->

        <div>



            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 text-center">



                <!-- Avatar -->

                <div class="relative w-32 h-32 mx-auto mb-4">

                    <div class="rounded-full bg-blue-600 text-white flex items-center justify-center w-full h-full text-4xl font-bold">

                        <?= strtoupper(substr($usuario->nombre, 0, 1) . substr($usuario->apellido, 0, 1)) ?>

                    </div>



                    <!-- Estado -->

                    <span class="absolute bottom-0 right-0 px-3 py-1 rounded-full text-xs font-semibold shadow 

                        <?= $usuario->id_estado == 1 ? 'bg-green-500 text-white' : 'bg-red-500 text-white' ?>">

                        <?= $usuario->nombre_estado ?>

                    </span>

                </div>



                <h2 class="text-xl font-bold text-gray-800 dark:text-white">

                    <?= $usuario->nombre . ' ' . $usuario->apellido ?>

                </h2>



                <p class="text-gray-500 dark:text-gray-300 mb-3"><?= $usuario->nombre_rol ?></p>



                <!-- Contactos -->

                <div class="flex justify-center gap-3 mb-4">

                    <a href="mailto:<?= $usuario->correo ?>" 

                        class="p-2 rounded-full bg-gray-100 dark:bg-gray-700">

                        <i class="fa-solid fa-envelope text-blue-600"></i>

                    </a>



                    <?php if ($usuario->telefono): ?>

                    <a href="tel:<?= $usuario->telefono ?>" 

                        class="p-2 rounded-full bg-gray-100 dark:bg-gray-700">

                        <i class="fa-solid fa-phone text-green-500"></i>

                    </a>

                    <?php endif; ?>

                </div>



                <!-- Botón de cambiar estado -->

                <button onclick="cambiarEstadoUsuario(<?= $usuario->id_usuario ?>, <?= $usuario->id_estado == 1 ? 0 : 1 ?>)"

                    class="w-full py-2 rounded-lg text-white 

                        <?= $usuario->id_estado == 1 ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' ?>">

                    <i class="fa-solid fa-user-gear mr-1"></i>

                    <?= $usuario->id_estado == 1 ? 'Desactivar Usuario' : 'Activar Usuario' ?>

                </button>



            </div>



            <!-- INFORMACIÓN ADICIONAL -->

            <div class="bg-white dark:bg-gray-800 mt-4 rounded-xl shadow p-6">

                <h3 class="text-gray-700 dark:text-white font-semibold mb-3">

                    <i class="fa-solid fa-info-circle mr-1"></i>Información Adicional

                </h3>



                <p class="text-sm text-gray-400">Último acceso</p>

                <p class="text-gray-800 dark:text-white font-medium">

                    <?= $usuario->ultimo_acceso ? date('d/m/Y H:i', strtotime($usuario->ultimo_acceso)) : 'Nunca' ?>

                </p>



                <p class="text-sm text-gray-400 mt-3">Fecha de registro</p>

                <p class="text-gray-800 dark:text-white font-medium">

                    <?= date('d/m/Y', strtotime($usuario->fecha_creacion)) ?>

                </p>

            </div>



        </div>



        <!-- INFORMACIÓN PERSONAL -->

        <div class="lg:col-span-2">



            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-6">



                <h3 class="text-gray-700 dark:text-white font-semibold mb-4">

                    <i class="fa-solid fa-id-card mr-2"></i>Información Personal

                </h3>



                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">



                    <div>

                        <p class="text-gray-400 text-sm">Nombre</p>

                        <p class="text-gray-800 dark:text-white font-medium"><?= $usuario->nombre ?></p>

                    </div>



                    <div>

                        <p class="text-gray-400 text-sm">Apellido</p>

                        <p class="text-gray-800 dark:text-white font-medium"><?= $usuario->apellido ?></p>

                    </div>



                    <div>

                        <p class="text-gray-400 text-sm">Fecha de nacimiento</p>

                        <p class="text-gray-800 dark:text-white font-medium">

                            <?= !empty($usuario->fecha_nacimiento) ? date('d/m/Y', strtotime($usuario->fecha_nacimiento)) : 'No especificada' ?>

                        </p>

                    </div>



                    <div>

                        <p class="text-gray-400 text-sm">Género</p>

                        <p class="text-gray-800 dark:text-white font-medium">

                            <?php
                                $generos = [
                                    1 => 'Masculino',
                                    2 => 'Femenino',
                                    3 => 'Otro'
                                ];
                                echo isset($usuario->id_genero, $generos[$usuario->id_genero]) ? $generos[$usuario->id_genero] : 'No especificado';
                            ?>

                        </p>

                    </div>

 <div>

                        <p class="text-gray-400 text-sm">Estado civil</p>

                        <p class="text-gray-800 dark:text-white font-medium">

                            <?php
                                $estados_civiles = [
                                    1 => 'Soltero(a)',
                                    2 => 'Casado(a)',
                                    3 => 'Unión libre',
                                    4 => 'Divorciado(a)',
                                    5 => 'Viudo(a)'
                                ];
                                echo isset($usuario->id_estado_civil, $estados_civiles[$usuario->id_estado_civil]) ? $estados_civiles[$usuario->id_estado_civil] : 'No especificado';
                            ?>

                        </p>

                    </div>

                    <div>

                        <p class="text-gray-400 text-sm">Correo</p>

                        <a href="mailto:<?= $usuario->correo ?>" 

                            class="text-blue-600 dark:text-blue-400 underline">

                            <?= $usuario->correo ?>

                        </a>

                    </div>



                    <div>

                        <p class="text-gray-400 text-sm">Teléfono</p>

                        <p class="text-gray-800 dark:text-white font-medium">

                            <?= $usuario->telefono ?: 'No especificado' ?>

                        </p>

                    </div>



                </div>



                <p class="mt-4 text-gray-400 text-sm">Dirección</p>

                <p class="text-gray-800 dark:text-white font-medium">

                    <?= $usuario->direccion ?: 'No especificada' ?>

                </p>



                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">



                    <div>

                        <p class="text-gray-400 text-sm">Rol</p>

                        <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200 text-sm font-medium">

                            <i class="fa-solid fa-user-shield mr-1"></i><?= $usuario->nombre_rol ?>

                        </span>

                    </div>



                    <div>

                        <p class="text-gray-400 text-sm">Estado</p>

                        <span class="px-3 py-1 rounded-full text-white text-sm font-medium 

                            <?= $usuario->id_estado == 1 ? 'bg-green-600' : 'bg-red-600' ?>">

                            <?= $usuario->nombre_estado ?>

                        </span>

                    </div>



                </div>



            </div>



            <!-- ACTIVIDAD -->

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 text-center text-gray-500 dark:text-gray-300 py-10">

                <i class="fa-solid fa-chart-line text-4xl mb-3"></i>

                <p>Próximamente: Historial de actividad del usuario</p>

            </div>

        </div>



    </div>



</div>