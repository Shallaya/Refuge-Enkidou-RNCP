<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Refuge Enkidou');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkTo(PetTypeCrudController::class, 'Types d\'animaux', 'fa-solid fa-paw');
        yield MenuItem::linkTo(CategoryCrudController::class, 'Categories', 'fa-solid fa-tags');
        yield MenuItem::linkTo(ProductCrudController::class, 'Produits', 'fa-solid fa-box');
        yield MenuItem::linkTo(ProductVariantCrudController::class, 'Variantes de produits', 'fa-solid fa-cube');
        yield MenuItem::linkTo(TagCrudController::class, 'Tags', 'fa-solid fa-tags');
        yield MenuItem::linkTo(EcoLabelCrudController::class, 'Labels écologiques', 'fa-solid fa-leaf');
        yield MenuItem::linkTo(PromotionCrudController::class, 'Promotions', 'fa-solid fa-tags');
        yield MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fa-solid fa-users');
        // yield MenuItem::linkTo(OrderCrudController::class, 'Commandes', 'fa-solid fa-shopping-cart');
        // yield MenuItem::linkTo(CommentCrudController::class, 'Commentaires', 'fa-solid fa-comments');
        
        yield MenuItem::section();

        // Lien de redirection vers la page d'accueil du site
        yield MenuItem::linkToRoute('retour au site', 'fa fa-undo', 'home');
        // Lien de déconnexion
        yield MenuItem::linkToLogout('Se déconnecter', 'fa fa-sign-out');      
        
        
        
        
        
        // ! exemple de liens supplémentaires à ajouter dans le menu admin
        
        // yield MenuItem::linkTo(ReviewCrudController::class, 'Avis', 'fa-solid fa-star');
        // yield MenuItem::linkTo(ContactMessageCrudController::class, 'Messages', 'fa-solid fa-envelope');
        // yield MenuItem::linkTo(PageCrudController::class, 'Pages', 'fa-solid fa-file');
        // yield MenuItem::linkTo(SettingCrudController::class, 'Paramètres', 'fa-solid fa-cogs');
        // yield MenuItem::linkTo(StatisticCrudController::class, 'Statistiques', 'fa-solid fa-chart-bar');
        // yield MenuItem::linkTo(LogEntryCrudController::class, 'Logs', 'fa-solid fa-clipboard-list');
        // yield MenuItem::linkTo(NewsletterSubscriberCrudController::class, 'Newsletter', 'fa-solid fa-newspaper');
        // yield MenuItem::linkTo(FaqCrudController::class, 'FAQ', 'fa-solid fa-question-circle');
        // yield MenuItem::linkTo(GalleryCrudController::class, 'Galerie', 'fa-solid fa-image');
        // yield MenuItem::linkTo(EventCrudController::class, 'Événements', 'fa-solid fa-calendar-alt');
        // yield MenuItem::linkTo(PartnerCrudController::class, 'Partenaires', 'fa-solid fa-handshake');
        // yield MenuItem::linkTo(VolunteerCrudController::class, 'Bénévoles', 'fa-solid fa-users-cog');
        // yield MenuItem::linkTo(DonationCrudController::class, 'Dons', 'fa-solid fa-donate');
        // yield MenuItem::linkTo(AdoptionCrudController::class, 'Adoptions', 'fa-solid fa-heart');
        // yield MenuItem::linkTo(ReportCrudController::class, 'Rapports', 'fa-solid fa-file-alt');
        // yield MenuItem::linkTo(SupporterCrudController::class, 'Soutiens', 'fa-solid fa-hands-helping');
        // yield MenuItem::linkTo(TestimonialCrudController::class, 'Témoignages', 'fa-solid fa-comment-dots');
        // yield MenuItem::linkTo(SubscriberCrudController::class, 'Abonnés', 'fa-solid fa-users');
        // yield MenuItem::linkTo(RoleCrudController::class, 'Rôles', 'fa-solid fa-user-shield');
        // yield MenuItem::linkTo(PermissionCrudController::class, 'Permissions', 'fa-solid fa-key');
        // yield MenuItem::linkTo(ThemeCrudController::class, 'Thèmes', 'fa-solid fa-palette');
        // yield MenuItem::linkTo(WidgetCrudController::class, 'Widgets', 'fa-solid fa-th-large');
        // yield MenuItem::linkTo(MediaCrudController::class, 'Médias', 'fa-solid fa-photo-video');
        // yield MenuItem::linkTo(NotificationCrudController::class, 'Notifications', 'fa-solid fa-bell');
        // yield MenuItem::linkTo(ErrorReportCrudController::class, 'Rapports d\'erreurs', 'fa-solid fa-bug');
        // yield MenuItem::linkTo(SecurityLogCrudController::class, 'Logs de sécurité', 'fa-solid fa-shield-alt');
        // yield MenuItem::linkTo(PerformanceLogCrudController::class, 'Logs de performance', 'fa-solid fa-tachometer-alt');
        // yield MenuItem::linkTo(AccessLogCrudController::class, 'Logs d\'accès', 'fa-solid fa-door-open');
        // yield MenuItem::linkTo(ApplicationLogCrudController::class, 'Logs d\'application', 'fa-solid fa-cogs');
        // yield MenuItem::linkTo(DatabaseLogCrudController::class, 'Logs de base de données', 'fa-solid fa-database');
        // yield MenuItem::linkTo(ServerLogCrudController::class, 'Logs de serveur', 'fa-solid fa-server');
        // yield MenuItem::linkTo(DebugLogCrudController::class, 'Logs de débogage', 'fa-solid fa-bug');
        // yield MenuItem::linkTo(AuditLogCrudController::class, 'Logs d\'audit', 'fa-solid fa-clipboard-list');
        // yield MenuItem::linkTo(TransactionLogCrudController::class, 'Logs de transaction', 'fa-solid fa-exchange-alt');
        // yield MenuItem::linkTo(SystemLogCrudController::class, 'Logs de système', 'fa-solid fa-cogs');
        // yield MenuItem::linkTo(NetworkLogCrudController::class, 'Logs de réseau', 'fa-solid fa-network-wired');
        // yield MenuItem::linkTo(SiteSettingCrudController::class, 'Paramètres du site', 'fa-solid fa-cogs');
        
        // yield MenuItem::linkToCrud('Types d\'animaux', 'fas fa-paw', Category::class);
        // yield MenuItem::linkToCrud('Catégories', 'fas fa-list', Category::class);
        // yield MenuItem::linkToCrud('Produits', 'fas fa-box', Product::class);
        // yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        // yield MenuItem::linkToCrud('Commandes', 'fas fa-shopping-cart', Order::class);
        // yield MenuItem::linkToCrud('Commentaires', 'fas fa-comments', Comment::class);
        // yield MenuItem::linkToCrud('Promotions', 'fas fa-tags', Promotion::class);
        // yield MenuItem::linkToCrud('Avis', 'fas fa-star', Review::class);
        // yield MenuItem::linkToCrud('Messages', 'fas fa-envelope', ContactMessage::class);
        // yield MenuItem::linkToCrud('Pages', 'fas fa-file', Page::class);
        // yield MenuItem::linkToCrud('Paramètres', 'fas fa-cogs', Setting::class);
        // yield MenuItem::linkToCrud('Statistiques', 'fas fa-chart-bar', Statistic::class);
        // yield MenuItem::linkToCrud('Logs', 'fas fa-clipboard-list', LogEntry::class);
        // yield MenuItem::linkToCrud('Newsletter', 'fas fa-newspaper', NewsletterSubscriber::class);
        // yield MenuItem::linkToCrud('FAQ', 'fas fa-question-circle', Faq::class);
        // yield MenuItem::linkToCrud('Galerie', 'fas fa-image', Gallery::class);
        // yield MenuItem::linkToCrud('Événements', 'fas fa-calendar-alt', Event::class);
        // yield MenuItem::linkToCrud('Partenaires', 'fas fa-handshake', Partner::class);
        // yield MenuItem::linkToCrud('Bénévoles', 'fas fa-users-cog', Volunteer::class);
        // yield MenuItem::linkToCrud('Dons', 'fas fa-donate', Donation::class);
        // yield MenuItem::linkToCrud('Adoptions', 'fas fa-heart', Adoption::class);
        // yield MenuItem::linkToCrud('Rapports', 'fas fa-file-alt', Report::class);
        // yield MenuItem::linkToCrud('Soutiens', 'fas fa-hands-helping', Supporter::class);
        // yield MenuItem::linkToCrud('Témoignages', 'fas fa-comment-dots', Testimonial::class);
        // yield MenuItem::linkToCrud('Abonnés', 'fas fa-users', Subscriber::class);
        // yield MenuItem::linkToCrud('Rôles', 'fas fa-user-shield', Role::class);
        // yield MenuItem::linkToCrud('Permissions', 'fas fa-key', Permission::class);
        // yield MenuItem::linkToCrud('Logs d\'activité', 'fas fa-history', ActivityLog::class);
        // yield MenuItem::linkToCrud('Paramètres du site', 'fas fa-cogs', SiteSetting::class);
        // yield MenuItem::linkToCrud('Thèmes', 'fas fa-palette', Theme::class);
        // yield MenuItem::linkToCrud('Widgets', 'fas fa-th-large', Widget::class);
        // yield MenuItem::linkToCrud('Médias', 'fas fa-photo-video', Media::class);
        // yield MenuItem::linkToCrud('Notifications', 'fas fa-bell', Notification::class);
        // yield MenuItem::linkToCrud('Rapports d\'erreurs', 'fas fa-bug', ErrorReport::class);
        // yield MenuItem::linkToCrud('Logs de sécurité', 'fas fa-shield-alt', SecurityLog::class);
        // yield MenuItem::linkToCrud('Logs de performance', 'fas fa-tachometer-alt', PerformanceLog::class);
        // yield MenuItem::linkToCrud('Logs d\'accès', 'fas fa-door-open', AccessLog::class);
        // yield MenuItem::linkToCrud('Logs d\'application', 'fas fa-cogs', ApplicationLog::class);
        // yield MenuItem::linkToCrud('Logs de base de données', 'fas fa-database', DatabaseLog::class);
        // yield MenuItem::linkToCrud('Logs de serveur', 'fas fa-server', ServerLog::class);
        // yield MenuItem::linkToCrud('Logs de débogage', 'fas fa-bug', DebugLog::class);
        // yield MenuItem::linkToCrud('Logs d\'audit', 'fas fa-clipboard-list', AuditLog::class);
        // yield MenuItem::linkToCrud('Logs de transaction', 'fas fa-exchange-alt', TransactionLog::class);
        // yield MenuItem::linkToCrud('Logs de système', 'fas fa-cogs', SystemLog::class);
        // yield MenuItem::linkToCrud('Logs de réseau', 'fas fa-network-wired', NetworkLog::class);
    }
}
