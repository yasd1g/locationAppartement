<?php

namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

/**
 * Classe pagination qui extrait toute notion de calcul et de récupération de données de nos controllers
 *
 * Elle nécessite après instanciation qu'on lui passe l'entité sur laquelle on souhaite travailler
 */
class PaginationService
{

    /**
     * Le nom de l'entité sur laquelle on veut effectuer une pagination
     *
     * @var
     */
    private $entityClass;

    /**
     * Le nombre d'enregistrement à récupérer
     *
     * @var int
     */
    private $limit = 10;

    /**
     * La page sur laquelle on se trouve actuellement
     *
     * @var
     */
    private $currentPage;

    /**
     * Le manager de Doctrine qui nous permet notamment de trouver le repository dont on a besoin
     *
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * Le nom de la route que l'on veut utiliser pour les boutons de navigation de la pagination
     *
     * @var mixed
     */
    private $route;

    /**
     * Le moteur de template Twig qui va permettre de générer le rendu de la pagination
     *
     * @var Environment
     */
    private $twig;

    /**
     * Le chemin vers le template qui contient la pagination
     *
     * @var
     */
    private $templatePath;


    /**
     * Constructeur du service de pagination qui sera appelé par Symfony
     *
     * N'oubliez pas de configurer votre fichier services.yaml afin que Symfony sache quelle valeur utiliser pour le $templatePath
     *
     * @param EntityManagerInterface $manager
     * @param Environment            $twig
     * @param RequestStack           $request
     * @param                        $templatePath
     */
    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request, $templatePath)
    {
        //on récupère le nom de la route à utiliser à partir des attribues de la requête actuelle
        $this->route   = $request->getCurrentRequest()->attributes->get('_route');
        // autres initialisations
        $this->manager = $manager;
        $this->twig    = $twig;
        $this->templatePath   = $templatePath;
    }


    /**
     * Permet de récupérer les données paginées pour une entité spécifique ( exemple Comment, User, Ad ..)
     *
     * Elle se sert de Doctrine afin de récupérer le repository de l'entité spécifique
     * puis grâce au repository et sa méthode findBy() on récupère les données dans une certaine limite et en partant d'un offset (début)
     *
     * @return object[]
     * @throws \Exception si la propriété $entityClass n'est pas configurée
     */
    public function getData()
    {
        // on ajoute une exeption dans le cas  on mentionne pas l'entityClass dans le controlleur par erreur

        if (empty($this->entityClass)){

            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer !
             Utiliser la méthode setEntityClass() de votre objet PaginationService !");
        }

        // 1) calculer l'offset ( début)

        $offset = $this->currentPage * $this->limit - $this->limit;
        // 2) demander au repository de trouver les elements

        $repo = $this->manager->getRepository($this->entityClass);
        // on utilise findBy() elle prend 4 arguments 1) tableau de critères 2) tableau des  orders 3) limite 4) offset ( début)
        $data = $repo->findBy([],[],$this->limit,$offset);

        // 3) retourner les elements en question
        return $data;
    }

    /**
     * Permet d'afficher le rendu de la navigation au sein d'un template twig
     *
     * On se sert ici de notre moteur de rendu Twig afin de compiler le template qui setrouve au chemin de notre propriété $templatePath, en lui passant
     * les variables :
     *  - page => la page actuelle sur laquelle on se trouve
     *  - pages => le nombre total de pages qui existent
     *  - route => le nom de la route à utiliser pour les liens de navigation
     *
     * Attention :  Cette fonction ne rretourne rien, elle affiche directement le rendu
     */
    public function display()
    {
        $this->twig->display($this->templatePath, [
            'page'  => $this->getPage(),
            'pages' =>$this->getPages(),
            'route' => $this->route

        ]);
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }

    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }


    public function getLimit()
    {
        return $this->limit;
    }


    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }


    public function getPage()
    {
        return $this->currentPage;
    }


    public function setPage($currentPage)
    {
        $this->currentPage = $currentPage;

        return $this;
    }


    /**
     * Permet de récupérer le nombre de pages qui existent sur une entité particulière
     *
     * Elle se sert de Doctrine pour récupérer le repository qui correspond à l'entité que l'on souhaite
     * paginer (voir la propriété $entityClass) puis elle trouve le nombre total de'enregistrements grâce à la fonction findAll() du repository
     *
     *
     * @throws \Exception si la propriété $entityClass n'est pas configurée
     */
    public function getPages()
    {
        // on ajoute une exeption dans le cas  on mentionne pas l'entityClass dans le controlleur par erreur

        if (empty($this->entityClass)){

            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer !
             Utiliser la méthode setEntityClass() de votre objet PaginationService !");
        }

        // 1) obtenir le total des enregistrements de la table

        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());
        // 2) calculer le nombre de page et l'arrondir

        $pages = ceil($total/$this->limit);

        return $pages;

    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }


    public function getTemplatePath()
    {
        return $this->templatePath;
    }


    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;

        return $this;
    }


}