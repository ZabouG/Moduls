<?php 
include('./components/navbar.php');
?>

<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <div class="flex">
        <?php include('./components/sidebar.php');?>
        
        <div class="flex-1 p-6">
            <!-- Your home page content goes here -->
            <h1 class="text-2xl font-semibold text-gray-700 mb-4">Bienvenue sur Moduls</h1>
            <p class="text-gray-600">Votre contenu principal ici...</p>
        
        <div> 
            <!-- A généré dynamiquement avec la fonction  dans frontend.function.addModuls.js-->
              <div class="relative max-w-xs border border-solid border-gray-200 rounded-2xl transition-all duration-500 ">
                <div class="block overflow-hidden">
                <img src="https://pagedone.io/asset/uploads/1695365240.png" alt="Card image" />
                </div>
                <div class="p-4">
                <h4 class="text-base font-semibold text-gray-900 mb-2 capitalize transition-all duration-500 ">Titre</h4>
                <p class="text-sm font-normal text-gray-500 transition-all duration-500 leading-5 mb-5"> Lorem, ipsum dolor sit amet consectetur adipisicing elit. Pariatur in </p>
                <button class="bg-indigo-600 shadow-sm rounded-full py-2 px-5 text-xs text-white font-semibold">Lire</button>
                </div>
                </div>
        </div>
        </div>
        
    </div>
</body>
</html>