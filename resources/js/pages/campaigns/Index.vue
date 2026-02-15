<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Search, Eye } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { type BreadcrumbItem } from '@/types';

type Category = {
    id: number;
    name: string;
};

type Campaign = {
    id: number;
    title: string;
    slug: string;
    description: string;
    goal_amount: string;
    current_amount: string;
    start_date: string;
    end_date: string;
    status: 'draft' | 'active' | 'funded' | 'closed';
    image: string | null;
    user: { id: number; name: string };
    category: Category | null;
    contributions_count: number;
};

type PaginatedData<T> = {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
};

const props = defineProps<{
    campaigns: PaginatedData<Campaign>;
    categories: Category[];
    filters: { search?: string; category?: string; status?: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Tableau de bord', href: '/dashboard' },
    { title: 'Campagnes', href: '/campaigns' },
];

const search = ref(props.filters.search ?? '');
const categoryFilter = ref(props.filters.category ?? '');
const statusFilter = ref(props.filters.status ?? '');

let debounceTimer: ReturnType<typeof setTimeout>;

watch([search, categoryFilter, statusFilter], () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(
            '/campaigns',
            {
                search: search.value || undefined,
                category: categoryFilter.value || undefined,
                status: statusFilter.value || undefined,
            },
            { preserveState: true, preserveScroll: true },
        );
    }, 300);
});

const statusLabels: Record<string, string> = {
    draft: 'Brouillon',
    active: 'Active',
    funded: 'Financée',
    closed: 'Clôturée',
};

const statusVariants: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    draft: 'outline',
    active: 'default',
    funded: 'secondary',
    closed: 'destructive',
};

function progressPercentage(campaign: Campaign): number {
    const goal = parseFloat(campaign.goal_amount);
    if (goal <= 0) return 0;
    return Math.min(100, Math.round((parseFloat(campaign.current_amount) / goal) * 100));
}

function formatCurrency(value: string | number): string {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(Number(value));
}
</script>

<template>
    <Head title="Campagnes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <Heading title="Campagnes" description="Parcourez et gérez les campagnes de financement." />
                <Link href="/campaigns/create">
                    <Button>
                        <Plus class="size-4" />
                        Nouvelle campagne
                    </Button>
                </Link>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap gap-3">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        v-model="search"
                        placeholder="Rechercher une campagne..."
                        class="pl-9"
                    />
                </div>
                <select
                    v-model="categoryFilter"
                    class="h-9 rounded-md border bg-background px-3 text-sm"
                >
                    <option value="">Toutes les catégories</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                        {{ cat.name }}
                    </option>
                </select>
                <select
                    v-model="statusFilter"
                    class="h-9 rounded-md border bg-background px-3 text-sm"
                >
                    <option value="">Tous les statuts</option>
                    <option value="draft">Brouillon</option>
                    <option value="active">Active</option>
                    <option value="funded">Financée</option>
                    <option value="closed">Clôturée</option>
                </select>
            </div>

            <!-- Campaigns grid -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="campaign in campaigns.data" :key="campaign.id" class="flex flex-col">
                    <CardHeader class="pb-3">
                        <div class="flex items-start justify-between gap-2">
                            <CardTitle class="line-clamp-2 text-base">{{ campaign.title }}</CardTitle>
                            <Badge :variant="statusVariants[campaign.status]">
                                {{ statusLabels[campaign.status] }}
                            </Badge>
                        </div>
                        <p v-if="campaign.category" class="text-xs text-muted-foreground">
                            {{ campaign.category.name }}
                        </p>
                    </CardHeader>
                    <CardContent class="flex-1">
                        <p class="mb-4 line-clamp-2 text-sm text-muted-foreground">
                            {{ campaign.description }}
                        </p>
                        <!-- Progress bar -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="font-medium">{{ formatCurrency(campaign.current_amount) }}</span>
                                <span class="text-muted-foreground">{{ formatCurrency(campaign.goal_amount) }}</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-secondary">
                                <div
                                    class="h-full rounded-full bg-primary transition-all duration-500"
                                    :style="{ width: progressPercentage(campaign) + '%' }"
                                />
                            </div>
                            <div class="flex justify-between text-xs text-muted-foreground">
                                <span>{{ progressPercentage(campaign) }}%</span>
                                <span>{{ campaign.contributions_count }} contribution{{ campaign.contributions_count > 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter class="pt-0">
                        <div class="flex w-full items-center justify-between">
                            <span class="text-xs text-muted-foreground">par {{ campaign.user.name }}</span>
                            <Link :href="`/campaigns/${campaign.id}`">
                                <Button variant="outline" size="sm">
                                    <Eye class="size-4" />
                                    Voir
                                </Button>
                            </Link>
                        </div>
                    </CardFooter>
                </Card>
            </div>

            <!-- Empty state -->
            <div
                v-if="campaigns.data.length === 0"
                class="flex flex-col items-center justify-center rounded-xl border border-dashed p-12 text-center"
            >
                <p class="text-muted-foreground">Aucune campagne trouvée.</p>
                <Link href="/campaigns/create" class="mt-4">
                    <Button variant="outline">
                        <Plus class="size-4" />
                        Créer une campagne
                    </Button>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="campaigns.last_page > 1" class="flex justify-center gap-1">
                <template v-for="link in campaigns.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="inline-flex h-9 items-center rounded-md px-3 text-sm transition-colors"
                        :class="link.active ? 'bg-primary text-primary-foreground' : 'hover:bg-accent'"
                        v-html="link.label"
                        preserve-scroll
                    />
                    <span
                        v-else
                        class="inline-flex h-9 items-center px-3 text-sm text-muted-foreground"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
