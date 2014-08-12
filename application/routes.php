<?php

use Zend_Controller_Router_Route as Route;
use Zend_Controller_Router_Route_Static as RouteStatic;

$routes = array(

    /*
    |--------------------------------------------------------------------------
    | Gestion de l'authentification
    |--------------------------------------------------------------------------
    */
    'login' => new RouteStatic('login', array('controller' => 'auth', 'action' => 'login')),
    'logout' => new RouteStatic('logout', array('controller' => 'auth', 'action' => 'logout')),

    /*
    |--------------------------------------------------------------------------
    | Tableau de bord
    |--------------------------------------------------------------------------
    */
    'root' => new RouteStatic('/', array('controller' => 'index', 'action' => 'index')),
    'home' => new RouteStatic('home', array('controller' => 'index', 'action' => 'index')),

    /*
    |--------------------------------------------------------------------------
    | Gestion du flux de messages
    |--------------------------------------------------------------------------
    */
    'message_add' => new RouteStatic('message/add', array('controller' => 'index', 'action' => 'add-message')),
    'message_delete' => new Route('message/delete/:id', array('controller' => 'index', 'action' => 'delete-message')),

    /*
    |--------------------------------------------------------------------------
    | Recherche
    |--------------------------------------------------------------------------
    */
    'search' => new RouteStatic('search', array('controller' => 'search', 'action' => 'etablissement')),
    'search_ets' => new RouteStatic('search/etablissements', array('controller' => 'search', 'action' => 'etablissement')),
    'search_doss' => new RouteStatic('search/dossiers', array('controller' => 'search', 'action' => 'dossier')),
    'search_courriers' => new RouteStatic('search/courriers', array('controller' => 'search', 'action' => 'courriers')),
    'search_display_ajax' => new RouteStatic('search/display-ajax-search', array('controller' => 'search', 'action' => 'display-ajax-search')),

    /*
    |--------------------------------------------------------------------------
    | Statistiques et extraction
    |--------------------------------------------------------------------------
    */
    'stats' => new RouteStatic('statistiques', array('controller' => 'statistiques', 'action' => 'index')),
    'stats_1' => new RouteStatic('statistiques/ccdsa-liste-erp-en-exploitation-connus-soumis-a-controle', array('controller' => 'statistiques', 'action' => 'ccdsa-liste-erp-en-exploitation-connus-soumis-a-controle')),
    'stats_2' => new RouteStatic('statistiques/liste-des-erp-sous-avis-defavorable', array('controller' => 'statistiques', 'action' => 'liste-des-erp-sous-avis-defavorable')),
    'stats_3' => new RouteStatic('statistiques/prochaines-visites-de-controle-periodique-a-faire-sur-une-commune', array('controller' => 'statistiques', 'action' => 'prochaines-visites-de-controle-periodique-a-faire-sur-une-commune')),
    'stats_4' => new RouteStatic('statistiques/liste-erp-avec-visite-periodique-sur-un-an', array('controller' => 'statistiques', 'action' => 'liste-erp-avec-visite-periodique-sur-un-an')),

    /*
    |--------------------------------------------------------------------------
    | Etablissements
    |--------------------------------------------------------------------------
    */
    'ets' => new Route('etablissement/:id', array('controller' => 'etablissement', 'action' => 'index')),
    'ets_edit' => new Route('etablissement/:id/edit', array('controller' => 'etablissement', 'action' => 'edit')),
    'ets_descriptifs' => new Route('etablissement/:id/descriptifs', array('controller' => 'etablissement', 'action' => 'descriptif')),
    'ets_descriptifs_edit' => new Route('etablissement/:id/descriptifs/edit', array('controller' => 'etablissement', 'action' => 'edit-descriptif')),
    'ets_textes_applicables' => new Route('etablissement/:id/textes-applicables', array('controller' => 'etablissement', 'action' => 'textes-applicables')),
    'ets_textes_applicables_edit' => new Route('etablissement/:id/textes-applicables/edit', array('controller' => 'etablissement', 'action' => 'edit-textes-applicables')),
    'ets_pieces_jointes' => new Route('etablissement/:id/pieces-jointes', array('controller' => 'etablissement', 'action' => 'pieces-jointes')),
    'ets_pieces_jointes_edit' => new Route('etablissement/:id/pieces-jointes/edit', array('controller' => 'etablissement', 'action' => 'edit-pieces-jointes')),
    'ets_pieces_jointes_add' => new Route('etablissement/:id/pieces-jointes/add', array('controller' => 'etablissement', 'action' => 'add-piece-jointe')),
    'ets_pieces_jointes_delete' => new Route('etablissement/:id/pieces-jointes/delete/:id_pj', array('controller' => 'etablissement', 'action' => 'delete-piece-jointe')),
    'ets_contacts' => new Route('etablissement/:id/contacts', array('controller' => 'etablissement', 'action' => 'contacts')),
    'ets_contacts_edit' => new Route('etablissement/:id/contacts/edit', array('controller' => 'etablissement', 'action' => 'edit-contacts')),
    'ets_contacts_add' => new Route('etablissement/:id/contacts/add', array('controller' => 'etablissement', 'action' => 'add-contact')),
    'ets_contacts_add_existant' => new Route('etablissement/:id/contacts/add-existant', array('controller' => 'etablissement', 'action' => 'add-contact-existant')),
    'ets_contacts_delete' => new Route('etablissement/:id/contacts/delete/:id_contact', array('controller' => 'etablissement', 'action' => 'delete-contact')),
    'ets_dossiers' => new Route('etablissement/:id/dossiers', array('controller' => 'etablissement', 'action' => 'dossiers')),
    'ets_historique' => new Route('etablissement/:id/historique', array('controller' => 'etablissement', 'action' => 'historique')),
    'ets_add' => new RouteStatic('etablissement/add', array('controller' => 'etablissement', 'action' => 'add')),

    /*
    |--------------------------------------------------------------------------
    | Commissions
    |--------------------------------------------------------------------------
    */
    'comm' => new Route('commission/:id', array('controller' => 'commission', 'action' => 'index')),
    'comm_edit' => new Route('commission/:id/edit', array('controller' => 'commission', 'action' => 'edit')),
    'comm_competences' => new Route('commission/:id/competences', array('controller' => 'commission', 'action' => 'competences')),
    'comm_competences_add' => new Route('commission/:id/competences/add', array('controller' => 'commission', 'action' => 'add-competence')),
    'comm_competences_edit' => new Route('commission/:id/competences/edit', array('controller' => 'commission', 'action' => 'edit-competences')),
    'comm_competences_delete' => new Route('commission/:id/competences/delete/:id_competence', array('controller' => 'commission', 'action' => 'delete-competence')),
    'comm_membres' => new Route('commission/:id/membres', array('controller' => 'commission', 'action' => 'membres')),
    'comm_membres_add' => new Route('commission/:id/membres/add', array('controller' => 'commission', 'action' => 'add-membre')),
    'comm_membres_edit' => new Route('commission/:id/membres/edit', array('controller' => 'commission', 'action' => 'edit-membres')),
    'comm_membres_delete' => new Route('commission/:id/membres/delete/:id_membre', array('controller' => 'commission', 'action' => 'delete-membre')),
    'comm_documents' => new Route('commission/:id/documents', array('controller' => 'commission', 'action' => 'documents')),
    'comm_documents_edit' => new Route('commission/:id/documents/edit', array('controller' => 'commission', 'action' => 'edit-documents')),
    'comm_contacts' => new Route('commission/:id/contacts', array('controller' => 'commission', 'action' => 'contacts')),
    'comm_contacts_edit' => new Route('commission/:id/contacts/edit', array('controller' => 'commission', 'action' => 'edit-contacts')),
    'comm_contacts_add' => new Route('commission/:id/contacts/add', array('controller' => 'commission', 'action' => 'add-contact')),
    'comm_contacts_add_existant' => new Route('commission/:id/contacts/add-existant', array('controller' => 'commission', 'action' => 'add-contact-existant')),
    'comm_contacts_delete' => new Route('commission/:id/contacts/delete/:id_contact', array('controller' => 'commission', 'action' => 'delete-contact')),
    'comm_calendrier' => new Route('commission/:id/calendrier', array('controller' => 'commission', 'action' => 'calendrier')),
    'comm_add' => new RouteStatic('commission/add', array('controller' => 'commission', 'action' => 'add')),
    'comm_delete' => new RouteStatic('commission/delete', array('controller' => 'commission', 'action' => 'delete')),

    /*
    |--------------------------------------------------------------------------
    | Date de passage en commissions
    |--------------------------------------------------------------------------
    */
    'datecomm' => new Route('commission/:id_commission/date/:id', array('controller' => 'date-commission', 'action' => 'index')),
    'datecomm_add' => new Route('commission/:id_commission/date/add', array('controller' => 'date-commission', 'action' => 'add')),

    /*
    |--------------------------------------------------------------------------
    | Dossiers
    |--------------------------------------------------------------------------
    */
    'doss' => new Route('dossier/:id', array('controller' => 'dossier', 'action' => 'index')),
    'doss_edit' => new Route('dossier/:id/edit', array('controller' => 'dossier', 'action' => 'edit')),
    'doss_documents_consultes' => new Route('dossier/:id/documents-consultes', array('controller' => 'dossier', 'action' => 'documents-consultes')),
    'doss_documents_consultes_edit' => new Route('dossier/:id/documents-consultes/edit', array('controller' => 'dossier', 'action' => 'edit-documents-consultes')),
    'doss_prescriptions' => new Route('dossier/:id/prescriptions', array('controller' => 'dossier', 'action' => 'prescriptions')),
    'doss_prescriptions_edit' => new Route('dossier/:id/prescriptions/edit', array('controller' => 'dossier', 'action' => 'edit-prescriptions')),
    'doss_prescriptions_add' => new Route('dossier/:id/prescriptions/add', array('controller' => 'dossier', 'action' => 'add-prescriptions')),
    'doss_prescriptions_delete' => new Route('dossier/:id/prescriptions/delete/:id_prescription', array('controller' => 'dossier', 'action' => 'delete-prescriptions')),
    'doss_textes_applicables' => new Route('dossier/:id/textes-applicables', array('controller' => 'dossier', 'action' => 'textes-applicables')),
    'doss_textes_applicables_edit' => new Route('dossier/:id/textes-applicables/edit', array('controller' => 'dossier', 'action' => 'edit-textes-applicables')),
    'doss_pieces_jointes' => new Route('dossier/:id/pieces-jointes', array('controller' => 'dossier', 'action' => 'pieces-jointes')),
    'doss_pieces_jointes_edit' => new Route('dossier/:id/pieces-jointes/edit', array('controller' => 'dossier', 'action' => 'edit-pieces-jointes')),
    'doss_pieces_jointes_add' => new Route('dossier/:id/pieces-jointes/add', array('controller' => 'dossier', 'action' => 'add-piece-jointe')),
    'doss_pieces_jointes_delete' => new Route('dossier/:id/pieces-jointes/delete/:id_pj', array('controller' => 'dossier', 'action' => 'delete-piece-jointe')),
    'doss_contacts' => new Route('dossier/:id/contacts', array('controller' => 'dossier', 'action' => 'contacts')),
    'doss_contacts_edit' => new Route('dossier/:id/contacts/edit', array('controller' => 'dossier', 'action' => 'edit-contacts')),
    'doss_contacts_add' => new Route('dossier/:id/contacts/add', array('controller' => 'dossier', 'action' => 'add-contact')),
    'doss_contacts_add_existant' => new Route('dossier/:id/contacts/add-existant', array('controller' => 'dossier', 'action' => 'add-contact-existant')),
    'doss_contacts_delete' => new Route('dossier/:id/contacts/delete/:id_contact', array('controller' => 'dossier', 'action' => 'delete-contact')),
    'doss_dossiers' => new Route('dossier/:id/dossiers', array('controller' => 'dossier', 'action' => 'dossiers')),
    'doss_etablissements' => new Route('dossier/:id/etablissements', array('controller' => 'dossier', 'action' => 'etablissements')),
    'doss_generation' => new Route('dossier/:id/generate', array('controller' => 'dossier', 'action' => 'generate')),
    'doss_add' => new Route('dossier/add/:id_etablissement', array('controller' => 'dossier', 'action' => 'add', 'id_etablissement' => null)),

    /*
    |--------------------------------------------------------------------------
    | Administration
    |--------------------------------------------------------------------------
    */
    'admin' => new RouteStatic('admin', array('module' => 'admin', 'controller' => 'index', 'action' => 'index')),
    'admin_login' => new RouteStatic('admin/login', array('module' => 'admin', 'controller' => 'auth', 'action' => 'login')),
    'admin_users' => new RouteStatic('admin/users', array('module' => 'admin', 'controller' => 'users', 'action' => 'index')),
    'admin_users_edit' => new Route('admin/users/:id/edit', array('module' => 'admin', 'controller' => 'users', 'action' => 'edit')),
    'admin_users_add' => new RouteStatic('admin/users/add', array('module' => 'admin', 'controller' => 'users', 'action' => 'add')),
    'admin_users_group_edit' => new Route('admin/users/group/:id/edit', array('module' => 'admin', 'controller' => 'users', 'action' => 'edit-group')),
    'admin_users_group_add' => new RouteStatic('admin/users/group/add', array('module' => 'admin', 'controller' => 'users', 'action' => 'add-group')),
    'admin_users_group_delete' => new Route('admin/users/group/delete/:id', array('module' => 'admin', 'controller' => 'users', 'action' => 'delete-group')),
    'admin_users_acl' => new RouteStatic('admin/users/matrice-des-droits', array('module' => 'admin', 'controller' => 'users', 'action' => 'matrice-des-droits')),
    'admin_users_res' => new RouteStatic('admin/users/ressources-specialisees', array('module' => 'admin', 'controller' => 'users', 'action' => 'ressources-specialisees')),
    'admin_users_res_add' => new RouteStatic('admin/users/ressources-specialisees/add', array('module' => 'admin', 'controller' => 'users', 'action' => 'add-ressources-specialisees')),
    'admin_users_res_delete' => new Route('admin/users/ressources-specialisees/delete/:id_resource', array('module' => 'admin', 'controller' => 'users', 'action' => 'delete-ressources-specialisees')),
    'admin_geo' => new RouteStatic('admin/cartographie-geographie', array('module' => 'admin', 'controller' => 'couches-cartographiques', 'action' => 'index')),
    'admin_geo_couches_edit' => new Route('admin/cartographie-geographie/couches-cartographiques/edit/:id', array('module' => 'admin', 'controller' => 'couches-cartographiques', 'action' => 'edit')),
    'admin_geo_couches_delete' => new Route('admin/cartographie-geographie/couches-cartographiques/delete/:id', array('module' => 'admin', 'controller' => 'couches-cartographiques', 'action' => 'delete')),
    'admin_geo_couches_add' => new RouteStatic('admin/cartographie-geographie/couches-cartographiques/add', array('module' => 'admin', 'controller' => 'couches-cartographiques', 'action' => 'add')),
    'admin_geo_groupements' => new RouteStatic('admin/cartographie-geographie', array('module' => 'admin', 'controller' => 'couches-cartographiques', 'action' => 'index')),
    'admin_prevention' => new RouteStatic('admin/prevention', array('module' => 'admin', 'controller' => 'tableau-des-periodicites', 'action' => 'index')),
    'admin_prevention_textes_applicables' => new RouteStatic('admin/textes-applicables', array('module' => 'admin', 'controller' => 'tableau-des-periodicites', 'action' => 'index')),

    /*
    |--------------------------------------------------------------------------
    | API 1.0
    |--------------------------------------------------------------------------
    */
    'api_test' => new RouteStatic('api/1.0/test', array('module' => 'api', 'controller' => 'dispatch', 'class' => 'Api_Service_Test', 'method' => 'test')),
    'api_ets' => new Route('api/1.0/etablissement/:method', array('module' => 'api', 'controller' => 'dispatch', 'class' => 'Api_Service_Etablissement', 'method' => 'get')),
    'api_search' => new Route('api/1.0/search/:method', array('module' => 'api', 'controller' => 'dispatch', 'class' => 'Api_Service_Search')),
    'api_contacts' => new RouteStatic('api/1.0/contacts', array('module' => 'api', 'controller' => 'dispatch', 'class' => 'Api_Service_Contact', 'method' => 'get')),
    'api_adresse' => new Route('api/1.0/adresse/:method', array('module' => 'api', 'controller' => 'dispatch', 'class' => 'Api_Service_Adresse', 'method' => 'get')),
    'api_commission' => new Route('api/1.0/commission/:method', array('module' => 'api', 'controller' => 'dispatch', 'class' => 'Api_Service_Commission', 'method' => 'get')),

    /*
    |--------------------------------------------------------------------------
    | Erreurs
    |--------------------------------------------------------------------------
    */
    'error' => new RouteStatic('error', array('controller' => 'error', 'action' => 'error')),

    /*
    |--------------------------------------------------------------------------
    | Téléchargement et visualisation de fichier
    |--------------------------------------------------------------------------
    */
    'download' => new RouteStatic('download', array('controller' => 'download', 'action' => 'index')),
    'view' => new RouteStatic('view', array('controller' => 'download', 'action' => 'view'))

);
