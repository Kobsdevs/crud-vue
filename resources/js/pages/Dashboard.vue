<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Megaphone, Heart, Users, Tag, TrendingUp, Target, DollarSign, Activity } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

type Stats = {
    totalCampaigns: number;
    activeCampaigns: number;
    fundedCampaigns: number;
    totalContributions: number;
    totalRaised: number;
    totalGoal: number;
    totalCategories: number;
    totalUsers: number;
};

type RecentCampaign = {
    id: number;
    title: string;
    slug: string;
    goal_amount: string;
    current_amount: string;
    status: 'draft' | 'active' | 'funded' | 'closed';
    progress: number;
    contributions_count: number;
    user: { name: string };
    category: { name: string } | null;
    created_at: string;
};

type RecentContribution = {
    id: number;
    amount: string;
    is_anonymous: boolean;
    user: { name: string } | null;
    campaign: { title: string };
    created_at: string;
};

const props = defineProps<{
    stats: Stats;
    recentCampaigns: RecentCampaign[];
    recentContributions: RecentContribution[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tableau de bord',
        href: dashboard().url,
    },
];

const formatCurrency = (value: number | string) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(Number(value));
};

const statusLabel: Record<string, string> = {
    draft: 'Brouillon',
    active: 'Active',
    funded: 'Financée',
    closed: 'Clôturée',
};

const statusVariant = (status: string): 'default' | 'secondary' | 'destructive' | 'outline' => {
    switch (status) {
        case 'active': return 'default';
        case 'funded': return 'secondary';
        case 'closed': return 'destructive';
        default: return 'outline';
    }
};

const globalProgress = props.stats.totalGoal > 0
    ? Math.min(100, Math.round((props.stats.totalRaised / props.stats.totalGoal) * 100))
    : 0;
</script>

<template>
    <Head title="Tableau de bord" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
            <!-- Stats Cards -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Campagnes</CardTitle>
                        <Megaphone class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalCampaigns }}</div>
                        <p class="text-xs text-muted-foreground">
                            {{ stats.activeCampaigns }} active{{ stats.activeCampaigns > 1 ? 's' : '' }} · {{ stats.fundedCampaigns }} financée{{ stats.fundedCampaigns > 1 ? 's' : '' }}
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Contributions</CardTitle>
                        <Heart class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalContributions }}</div>
                        <p class="text-xs text-muted-foreground">
                            dons enregistrés
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Montant Collecté</CardTitle>
                        <DollarSign class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ formatCurrency(stats.totalRaised) }}</div>
                        <p class="text-xs text-muted-foreground">
                            sur {{ formatCurrency(stats.totalGoal) }} objectif
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Progression Globale</CardTitle>
                        <TrendingUp class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ globalProgress }}%</div>
                        <div class="mt-2 h-2 w-full rounded-full bg-secondary">
                            <div
                                class="h-2 rounded-full bg-primary transition-all"
                                :style="{ width: globalProgress + '%' }"
                            />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Secondary Stats -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Catégories</CardTitle>
                        <Tag class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalCategories }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Utilisateurs</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalUsers }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Campagnes Actives</CardTitle>
                        <Activity class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.activeCampaigns }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Campagnes Financées</CardTitle>
                        <Target class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.fundedCampaigns }}</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Recent Data -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Recent Campaigns -->
                <Card>
                    <CardHeader>
                        <CardTitle>Campagnes récentes</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-if="recentCampaigns.length === 0" class="text-sm text-muted-foreground text-center py-4">
                            Aucune campagne pour le moment.
                        </div>
                        <div
                            v-for="campaign in recentCampaigns"
                            :key="campaign.id"
                            class="flex items-center justify-between gap-4"
                        >
                            <div class="min-w-0 flex-1">
                                <Link :href="'/campaigns/' + campaign.id" class="text-sm font-medium hover:underline truncate block">
                                    {{ campaign.title }}
                                </Link>
                                <div class="flex items-center gap-2 mt-1">
                                    <Badge :variant="statusVariant(campaign.status)" class="text-xs">
                                        {{ statusLabel[campaign.status] }}
                                    </Badge>
                                    <span class="text-xs text-muted-foreground">{{ campaign.created_at }}</span>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-sm font-medium">{{ formatCurrency(campaign.current_amount) }}</div>
                                <div class="text-xs text-muted-foreground">{{ campaign.progress }}%</div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Contributions -->
                <Card>
                    <CardHeader>
                        <CardTitle>Contributions récentes</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-if="recentContributions.length === 0" class="text-sm text-muted-foreground text-center py-4">
                            Aucune contribution pour le moment.
                        </div>
                        <div
                            v-for="contribution in recentContributions"
                            :key="contribution.id"
                            class="flex items-center justify-between gap-4"
                        >
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium truncate">
                                    {{ contribution.is_anonymous ? 'Anonyme' : contribution.user?.name }}
                                </div>
                                <div class="text-xs text-muted-foreground truncate">
                                    {{ contribution.campaign.title }} · {{ contribution.created_at }}
                                </div>
                            </div>
                            <div class="text-sm font-bold text-primary shrink-0">
                                {{ formatCurrency(contribution.amount) }}
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
