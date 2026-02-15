<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Heart, Megaphone, Shield, Users, Target, TrendingUp, ArrowRight } from 'lucide-vue-next';
import { dashboard, login, register } from '@/routes';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="FundFlow — Plateforme de financement participatif" />
    <div class="min-h-screen bg-white dark:bg-neutral-950 text-neutral-900 dark:text-neutral-100">
        <!-- Navigation -->
        <header class="sticky top-0 z-50 border-b border-neutral-200 bg-white/80 backdrop-blur-lg dark:border-neutral-800 dark:bg-neutral-950/80">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <Link href="/" class="flex items-center gap-2 text-xl font-bold tracking-tight">
                    <Heart class="h-6 w-6 text-primary" :stroke-width="2.5" />
                    <span>FundFlow</span>
                </Link>
                <nav class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2 text-sm font-medium text-primary-foreground shadow-sm transition hover:bg-primary/90"
                    >
                        Tableau de bord
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="rounded-lg px-5 py-2 text-sm font-medium transition hover:bg-neutral-100 dark:hover:bg-neutral-800"
                        >
                            Connexion
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2 text-sm font-medium text-primary-foreground shadow-sm transition hover:bg-primary/90"
                        >
                            S'inscrire
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 -z-10 bg-linear-to-br from-primary/5 via-transparent to-primary/5 dark:from-primary/10 dark:to-primary/10" />
            <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:py-40">
                <div class="mx-auto max-w-3xl text-center">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/5 px-4 py-1.5 text-sm font-medium text-primary">
                        <Heart class="h-4 w-4" />
                        Plateforme de financement participatif
                    </div>
                    <h1 class="text-4xl font-black tracking-tight sm:text-5xl lg:text-6xl">
                        Donnez vie à vos
                        <span class="text-primary">projets</span>
                        grâce à la communauté
                    </h1>
                    <p class="mt-6 text-lg leading-relaxed text-neutral-600 dark:text-neutral-400">
                        FundFlow connecte les porteurs de projets avec des contributeurs passionnés.
                        Créez votre campagne, partagez votre vision et recevez le soutien financier
                        dont vous avez besoin pour réaliser vos ambitions.
                    </p>
                    <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <Link
                            v-if="!$page.props.auth.user"
                            :href="register()"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary px-8 py-3 text-base font-semibold text-primary-foreground shadow-lg transition hover:bg-primary/90 hover:shadow-xl"
                        >
                            Commencer maintenant
                            <ArrowRight class="h-5 w-5" />
                        </Link>
                        <Link
                            v-else
                            :href="dashboard()"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary px-8 py-3 text-base font-semibold text-primary-foreground shadow-lg transition hover:bg-primary/90 hover:shadow-xl"
                        >
                            Accéder au tableau de bord
                            <ArrowRight class="h-5 w-5" />
                        </Link>
                        <a
                            href="#fonctionnalites"
                            class="inline-flex items-center gap-2 rounded-lg border border-neutral-300 px-8 py-3 text-base font-semibold transition hover:bg-neutral-50 dark:border-neutral-700 dark:hover:bg-neutral-800/50"
                        >
                            En savoir plus
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="border-y border-neutral-200 bg-neutral-50 dark:border-neutral-800 dark:bg-neutral-900/50">
            <div class="mx-auto grid max-w-7xl grid-cols-2 gap-8 px-6 py-16 lg:grid-cols-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary sm:text-4xl">Simple</div>
                    <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">Création de campagne en quelques clics</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary sm:text-4xl">Sécurisé</div>
                    <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">Contributions et données protégées</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary sm:text-4xl">Transparent</div>
                    <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">Suivi en temps réel des objectifs</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary sm:text-4xl">Solidaire</div>
                    <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">Communauté engagée et bienveillante</p>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="fonctionnalites" class="py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        Tout ce qu'il faut pour réussir votre campagne
                    </h2>
                    <p class="mt-4 text-lg text-neutral-600 dark:text-neutral-400">
                        Des outils puissants et intuitifs pour gérer vos campagnes de financement du début à la fin.
                    </p>
                </div>

                <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Feature 1 -->
                    <div class="group rounded-2xl border border-neutral-200 p-8 transition hover:border-primary/30 hover:shadow-lg dark:border-neutral-800 dark:hover:border-primary/30">
                        <div class="mb-4 inline-flex rounded-xl bg-primary/10 p-3 text-primary">
                            <Megaphone class="h-6 w-6" />
                        </div>
                        <h3 class="text-lg font-semibold">Créez vos campagnes</h3>
                        <p class="mt-2 text-sm leading-relaxed text-neutral-600 dark:text-neutral-400">
                            Lancez une campagne de financement en définissant un objectif, une durée et une description captivante pour attirer les contributeurs.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="group rounded-2xl border border-neutral-200 p-8 transition hover:border-primary/30 hover:shadow-lg dark:border-neutral-800 dark:hover:border-primary/30">
                        <div class="mb-4 inline-flex rounded-xl bg-primary/10 p-3 text-primary">
                            <Heart class="h-6 w-6" />
                        </div>
                        <h3 class="text-lg font-semibold">Recevez des contributions</h3>
                        <p class="mt-2 text-sm leading-relaxed text-neutral-600 dark:text-neutral-400">
                            Les contributeurs peuvent soutenir vos projets avec des dons, accompagnés de messages d'encouragement, même de manière anonyme.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="group rounded-2xl border border-neutral-200 p-8 transition hover:border-primary/30 hover:shadow-lg dark:border-neutral-800 dark:hover:border-primary/30">
                        <div class="mb-4 inline-flex rounded-xl bg-primary/10 p-3 text-primary">
                            <TrendingUp class="h-6 w-6" />
                        </div>
                        <h3 class="text-lg font-semibold">Suivez la progression</h3>
                        <p class="mt-2 text-sm leading-relaxed text-neutral-600 dark:text-neutral-400">
                            Visualisez en temps réel le montant collecté, le pourcentage atteint et le nombre de contributeurs grâce au tableau de bord.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="group rounded-2xl border border-neutral-200 p-8 transition hover:border-primary/30 hover:shadow-lg dark:border-neutral-800 dark:hover:border-primary/30">
                        <div class="mb-4 inline-flex rounded-xl bg-primary/10 p-3 text-primary">
                            <Target class="h-6 w-6" />
                        </div>
                        <h3 class="text-lg font-semibold">Organisez par catégories</h3>
                        <p class="mt-2 text-sm leading-relaxed text-neutral-600 dark:text-neutral-400">
                            Classez vos campagnes par catégorie pour une meilleure visibilité et permettre aux contributeurs de trouver les projets qui les intéressent.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="group rounded-2xl border border-neutral-200 p-8 transition hover:border-primary/30 hover:shadow-lg dark:border-neutral-800 dark:hover:border-primary/30">
                        <div class="mb-4 inline-flex rounded-xl bg-primary/10 p-3 text-primary">
                            <Shield class="h-6 w-6" />
                        </div>
                        <h3 class="text-lg font-semibold">Sécurité renforcée</h3>
                        <p class="mt-2 text-sm leading-relaxed text-neutral-600 dark:text-neutral-400">
                            Authentification à deux facteurs, gestion des rôles et protection des données personnelles pour une plateforme de confiance.
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="group rounded-2xl border border-neutral-200 p-8 transition hover:border-primary/30 hover:shadow-lg dark:border-neutral-800 dark:hover:border-primary/30">
                        <div class="mb-4 inline-flex rounded-xl bg-primary/10 p-3 text-primary">
                            <Users class="h-6 w-6" />
                        </div>
                        <h3 class="text-lg font-semibold">Tableau de bord complet</h3>
                        <p class="mt-2 text-sm leading-relaxed text-neutral-600 dark:text-neutral-400">
                            Accédez à une synthèse claire de vos chiffres : campagnes, contributions, montants collectés et progression globale en un coup d'œil.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section class="border-t border-neutral-200 bg-neutral-50 py-24 sm:py-32 dark:border-neutral-800 dark:bg-neutral-900/50">
            <div class="mx-auto max-w-7xl px-6">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        Comment ça marche ?
                    </h2>
                    <p class="mt-4 text-lg text-neutral-600 dark:text-neutral-400">
                        Trois étapes simples pour lancer votre campagne de financement.
                    </p>
                </div>

                <div class="mt-16 grid gap-12 lg:grid-cols-3">
                    <div class="text-center">
                        <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-primary text-2xl font-bold text-primary-foreground shadow-lg">
                            1
                        </div>
                        <h3 class="text-xl font-semibold">Créez votre compte</h3>
                        <p class="mt-3 text-neutral-600 dark:text-neutral-400">
                            Inscrivez-vous en quelques secondes et accédez à votre espace personnel sécurisé.
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-primary text-2xl font-bold text-primary-foreground shadow-lg">
                            2
                        </div>
                        <h3 class="text-xl font-semibold">Lancez une campagne</h3>
                        <p class="mt-3 text-neutral-600 dark:text-neutral-400">
                            Décrivez votre projet, fixez votre objectif financier et définissez la durée de votre campagne.
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-primary text-2xl font-bold text-primary-foreground shadow-lg">
                            3
                        </div>
                        <h3 class="text-xl font-semibold">Recevez des soutiens</h3>
                        <p class="mt-3 text-neutral-600 dark:text-neutral-400">
                            Partagez votre campagne et regardez les contributions affluer vers votre projet.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6">
                <div class="relative overflow-hidden rounded-3xl bg-primary px-8 py-16 text-center shadow-2xl sm:px-16 sm:py-24">
                    <div class="relative z-10">
                        <h2 class="text-3xl font-bold tracking-tight text-primary-foreground sm:text-4xl">
                            Prêt à donner vie à votre projet ?
                        </h2>
                        <p class="mx-auto mt-4 max-w-xl text-lg text-primary-foreground/80">
                            Rejoignez FundFlow et commencez à collecter des fonds pour concrétiser vos idées dès aujourd'hui.
                        </p>
                        <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                            <Link
                                v-if="!$page.props.auth.user"
                                :href="register()"
                                class="inline-flex items-center gap-2 rounded-lg bg-white px-8 py-3 text-base font-semibold text-primary shadow-lg transition hover:bg-neutral-100"
                            >
                                Créer mon compte gratuitement
                                <ArrowRight class="h-5 w-5" />
                            </Link>
                            <Link
                                v-else
                                :href="dashboard()"
                                class="inline-flex items-center gap-2 rounded-lg bg-white px-8 py-3 text-base font-semibold text-primary shadow-lg transition hover:bg-neutral-100"
                            >
                                Accéder au tableau de bord
                                <ArrowRight class="h-5 w-5" />
                            </Link>
                        </div>
                    </div>
                    <div class="absolute -top-24 -right-24 h-64 w-64 rounded-full bg-white/10" />
                    <div class="absolute -bottom-16 -left-16 h-48 w-48 rounded-full bg-white/10" />
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-neutral-200 dark:border-neutral-800">
            <div class="mx-auto max-w-7xl px-6 py-12">
                <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                    <div class="flex items-center gap-2 text-sm font-semibold">
                        <Heart class="h-4 w-4 text-primary" :stroke-width="2.5" />
                        FundFlow
                    </div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        &copy; {{ new Date().getFullYear() }} FundFlow. Tous droits réservés.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
