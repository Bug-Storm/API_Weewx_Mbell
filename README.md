# Api_Weewx_MBELL


# Préambule  

__Description__  

 Ce script permet d'importer  les  données de votre base de données afin de pouvoir exporter en format JSON.  Ce script est fait pour les stations fonctionnant sous le logiciel WeeWX ou d'autres ayant une base de données MySQL. Ceci a été adapté pour être utilisé avec MBELL. https://github.com/Networkbell/mbell


 __Requis__ 

 * Une station météo fonctionnant déjà avec une base des données MySQL.
 * Un accès en ligne de commande à votre Raspberry Pi. Si vous avez installé WeeWX ce ne devrait pas être un souci.   
 * Le CMS MBELL déjà installé. 



# __Installation__


 __Copie des fichiers__
 
 Se placer dans un premier temps dans le répertoire ou l'on veut copier le script, puis cloner le répertoire.  

 ` cd /var/www/html `  

 ` git clone https://github.com/Bug-Storm/API_Weewx_MBELL `    



 __Configuration__

On peut maintenant se placer dans le répertoire du script afin de modifier le fichier de configuration.

 ` cd /var/www/html/API_Weewx_MBELL `  
 
  ` nano Database.php `


 __Connection a la base des données__ 



 * Si vous avez une base de données MySQL, il va falloir renseigner les paramètres de connexion à la base :

  
  ` private $host = ''; `    
   `private $db_name = '';`     
   `private $username = ''; `    
   `private $password = ''; `  
   

----------------------------------------------------------------------------------------------------------------------------------------

  `* private $host:`   qui est l'adresse de l'hôte de la base de données. Probablement localhost si la base de données est hébergée sur votre Raspberry Pi.  

 `* private $db_name:`   le nom de la base de données. Par défaut le nom de la bdd  Weewx c'est  ` weewx `.  

 `* private $username:`   le nom d'utilisateur qu'à l'accès à la BDD.

 `* private $password:`   le mot de passe de cet utilisateur.  
 
 Pour sauvergarder il suffit de faire Ctrl + S -> Ctrl + X 

 
 __Creation d'un nouveau utilisateur__
 
 Pour créer un nouveau user il suffit d'ouvrir une ligne de commande et de taper:

 ` php newuser.php `

![Simplon.co](https://i.imgur.com/9kCwrKu.gif)


*************************************************************************************************************************
Si vous n'avez pas encore la table users, cela va se faire automatiquement.
-------------------------------------------------------------------------------------------------------------------------
Au cas où il y a un erreur, vous avez le fichier "users.sql". Cela vous permet d'importer le fichier sur phpmyadmin ;)
------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************

puis vous allez rentrer le Nom d'utilisateur que vous voulez, le nom de la station et puis la latitude/longitue(vous pouvez prendre celui sur IC). Le script va donc créer un Id + une API Key et une API Signature.  

Une fois le nouveau utilisateur créer, vous pouvez laisser la ligne de commande ouverte!!


__Recuperation des données__

Pour que l'api puisse bien récupérer les données de la BDD, vous avez besoin de 4 paramètres pour le mode "current": 

---------------------------------------------------------------------
t =  ` Timestamp(valable 5m)`

id = ` L'id correspondent au user `

api key = ` L'api Key qu'a été crée avec l'user `

api signature = ` L'api signature qu'a été crée avec l'user `

--------------------------------------------------------------------
Et pour le mode "historic" vous avez besoin de 6 paramètres:

t =  ` Timestamp(valable 5m)`

id = ` L'id correspondent au user `

api key = ` L'api Key qu'a été crée avec l'user `

api signature = ` L'api signature qu'a été crée avec l'user `

start timestamp = ` la Date/L'heure du début que vous voulez récupérer  `

end timestamp = ` la Date/L'heure de la fin  que vous voulez récupérer  `

------------------------------------------------------------------------

Vous devrez avoir l'url comme ça pour le mode current: 

`https://mydnsadresse/API_Weewx_Mbell/current.php?t=1613422447&id=1&apikey=555&apisignature=555 `

-------------------------------------------------------------------------

Vous devrez avoir l'url comme ça pour le mode historic:

`https://mydnsadresse/API_Weewx_Mbell/historic.php?t=1613422447&id=1&apikey=555&apisignature=555&starttimestamp=1613343600&endtimestamp=1613419937 `

--------------------------------------------------------------------------

Merci à https://nouvelle-techno.fr/actualites/live-coding-creer-une-api-rest
