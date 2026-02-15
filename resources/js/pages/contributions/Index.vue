<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

type Contribution = {
    id: number;
    amount: string;
    message: string | null;
    is_anonymous: boolean;
    created_at: string;
    campaign: {
        id: number;
        title: string;
        slug: string;
        status: string;
    };
    user: { id: number; name: string };
};

type PaginatedData<T> = {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
};

defineProps<{
    contributions: PaginatedData<Contribution>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Tableau de bord', href: '/dashboard' },
    { title: 'Mes contributions', href: '/contributions' },
];

const page = usePage();
const flash = page.props.flash as { success?: string } | undefined;

const showDeleteDialog = ref(false);
const contributionToDelete = ref<Contribution | null>(null);

function formatCurrency(value: string | number): string {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(Number(value));
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

function confirmDelete(contribution: Contribution) {
    contributionToDelete.value = contribution;
    showDeleteDialog.value = true;
}

function deleteContribution() {
    if (contributionToDelete.value) {
        router.delete(`/contributions/${contributionToDelete.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                showDeleteDialog.value = false;
                contributionToDelete.value = null;
            },
        });
    }
}

const statusLabels: Record<string, string> = {
    draft: 'Brouillon',
    active: 'Active',
    funded: 'Financée',
    closed: 'Clôturée',
};
</script>

<template>
    <Head title="Mes contributions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Heading title="Mes contributions" description="Historique de toutes vos contributions aux campagnes." />

            <!-- Flash message -->
            <div
                v-if="flash?.success"
                class="rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-950 dark:text-green-200"
            >
                {{ flash.success }}
            </div>

            <!-- Contributions list -->
            <div class="space-y-4">
                <Card v-for="contribution in contributions.data" :key="contribution.id">
                    <CardContent class="flex items-center justify-between p-4">
                        <div class="flex-1 space-y-1">
                            <div class="flex items-center gap-2">
                                <Link
                                    :href="`/campaigns/${contribution.campaign.id}`"
                                    class="font-medium hover:underline"
                                >
                                    {{ contribution.campaign.title }}
                                </Link>
                                <Badge variant="outline" class="text-xs">
                                    {{ statusLabels[contribution.campaign.status] ?? contribution.campaign.status }}
                                </Badge>
                                <Badge v-if="contribution.is_anonymous" variant="secondary" class="text-xs">
                                    Anonyme
                                </Badge>
                            </div>
                            <p v-if="contribution.message" class="text-sm text-muted-foreground">
                                « {{ contribution.message }} »
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ formatDate(contribution.created_at) }}
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-lg font-semibold text-primary">
                                {{ formatCurrency(contribution.amount) }}
                            </span>
                            <Button
                                variant="ghost"
                                size="icon-sm"
                                @click="confirmDelete(contribution)"
                            >
                                <Trash2 class="size-4 text-destructive" />
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Empty state -->
            <div
                v-if="contributions.data.length === 0"
                class="flex flex-col items-center justify-center rounded-xl border border-dashed p-12 text-center"
            >
                <p class="text-muted-foreground">Vous n'avez pas encore contribué à une campagne.</p>
                <Link href="/campaigns" class="mt-4">
                    <Button variant="outline">
                        <ArrowLeft class="size-4" />
                        Voir les campagnes
                    </Button>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="contributions.last_page > 1" class="flex justify-center gap-1">
                <template v-for="link in contributions.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="inline-flex h-9 items-center rounded-md px-3 text-sm transition-colors"
                        :class="link.active ? 'bg-primary text-primary-foreground' : 'hover:bg-accent'"
                        preserve-scroll
                    >
                        <!-- eslint-disable-next-line vue/no-v-html -->
                        <span v-html="link.label" />
                    </Link>
                    <span
                        v-else
                        class="inline-flex h-9 items-center px-3 text-sm text-muted-foreground"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>

        <!-- Delete confirmation dialog -->
        <Dialog v-model:open="showDeleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Annuler la contribution</DialogTitle>
                    <DialogDescription>
                        Êtes-vous sûr de vouloir annuler votre contribution de
                        {{ contributionToDelete ? formatCurrency(contributionToDelete.amount) : '' }}
                        à « {{ contributionToDelete?.campaign.title }} » ?
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="showDeleteDialog = false">Non, garder</Button>
                    <Button variant="destructive" @click="deleteContribution">Oui, annuler</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
