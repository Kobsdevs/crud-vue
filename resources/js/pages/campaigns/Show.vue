<script setup lang="ts">
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import { Pencil, Trash2, ArrowLeft, Heart } from 'lucide-vue-next';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

type Contribution = {
    id: number;
    amount: string;
    message: string | null;
    is_anonymous: boolean;
    created_at: string;
    user: { id: number; name: string };
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
    category: { id: number; name: string } | null;
    contributions: Contribution[];
};

const props = defineProps<{
    campaign: Campaign;
    progressPercentage: number;
    contributionsCount: number;
}>();

const page = usePage();
const currentUser = page.props.auth as { user: { id: number } };
const flash = page.props.flash as { success?: string } | undefined;
const isOwner = currentUser.user.id === props.campaign.user.id;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Tableau de bord', href: '/dashboard' },
    { title: 'Campagnes', href: '/campaigns' },
    { title: props.campaign.title },
];

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

// Contribution form
const contributionForm = useForm({
    amount: '' as string | number,
    message: '',
    is_anonymous: false,
});

function submitContribution() {
    contributionForm.post(`/campaigns/${props.campaign.id}/contributions`, {
        preserveScroll: true,
        onSuccess: () => contributionForm.reset(),
    });
}

// Delete dialog
const showDeleteDialog = ref(false);

function deleteCampaign() {
    router.delete(`/campaigns/${props.campaign.id}`, {
        onSuccess: () => {
            showDeleteDialog.value = false;
        },
    });
}
</script>

<template>
    <Head :title="campaign.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <!-- Back link -->
            <Link href="/campaigns" class="inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="size-4" />
                Retour aux campagnes
            </Link>

            <!-- Flash message -->
            <div
                v-if="flash?.success"
                class="rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-950 dark:text-green-200"
            >
                {{ flash.success }}
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Main content -->
                <div class="space-y-6 lg:col-span-2">
                    <Card>
                        <CardHeader>
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="mb-2 flex items-center gap-2">
                                        <Badge :variant="statusVariants[campaign.status]">
                                            {{ statusLabels[campaign.status] }}
                                        </Badge>
                                        <Badge v-if="campaign.category" variant="outline">
                                            {{ campaign.category.name }}
                                        </Badge>
                                    </div>
                                    <CardTitle class="text-2xl">{{ campaign.title }}</CardTitle>
                                    <p class="mt-1 text-sm text-muted-foreground">
                                        par {{ campaign.user.name }} · Du {{ formatDate(campaign.start_date) }} au {{ formatDate(campaign.end_date) }}
                                    </p>
                                </div>
                                <div v-if="isOwner" class="flex gap-2">
                                    <Link :href="`/campaigns/${campaign.id}/edit`">
                                        <Button variant="outline" size="icon-sm">
                                            <Pencil class="size-4" />
                                        </Button>
                                    </Link>
                                    <Button variant="destructive" size="icon-sm" @click="showDeleteDialog = true">
                                        <Trash2 class="size-4" />
                                    </Button>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div v-if="campaign.image" class="mb-6 overflow-hidden rounded-lg">
                                <img
                                    :src="`/storage/${campaign.image}`"
                                    :alt="campaign.title"
                                    class="h-64 w-full object-cover"
                                />
                            </div>
                            <p class="whitespace-pre-line text-sm leading-relaxed">{{ campaign.description }}</p>
                        </CardContent>
                    </Card>

                    <!-- Recent contributions -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg">
                                Contributions récentes ({{ contributionsCount }})
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="campaign.contributions.length > 0" class="space-y-4">
                                <div
                                    v-for="contribution in campaign.contributions"
                                    :key="contribution.id"
                                    class="flex items-start justify-between rounded-lg border p-3"
                                >
                                    <div>
                                        <p class="text-sm font-medium">
                                            {{ contribution.is_anonymous ? 'Anonyme' : contribution.user.name }}
                                        </p>
                                        <p v-if="contribution.message" class="mt-1 text-sm text-muted-foreground">
                                            {{ contribution.message }}
                                        </p>
                                        <p class="mt-1 text-xs text-muted-foreground">
                                            {{ formatDate(contribution.created_at) }}
                                        </p>
                                    </div>
                                    <span class="font-semibold text-primary">
                                        {{ formatCurrency(contribution.amount) }}
                                    </span>
                                </div>
                            </div>
                            <p v-else class="text-sm text-muted-foreground">
                                Aucune contribution pour le moment. Soyez le premier !
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Progress card -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg">Progression</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-primary">
                                    {{ formatCurrency(campaign.current_amount) }}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    collectés sur {{ formatCurrency(campaign.goal_amount) }}
                                </p>
                            </div>
                            <div class="space-y-2">
                                <div class="h-3 overflow-hidden rounded-full bg-secondary">
                                    <div
                                        class="h-full rounded-full bg-primary transition-all duration-500"
                                        :style="{ width: progressPercentage + '%' }"
                                    />
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="font-medium">{{ progressPercentage }}%</span>
                                    <span class="text-muted-foreground">{{ contributionsCount }} contributeur{{ contributionsCount > 1 ? 's' : '' }}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Contribution form -->
                    <Card v-if="campaign.status === 'active'">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2 text-lg">
                                <Heart class="size-5 text-red-500" />
                                Contribuer
                            </CardTitle>
                        </CardHeader>
                        <form @submit.prevent="submitContribution">
                            <CardContent class="space-y-4">
                                <div class="space-y-2">
                                    <Label for="amount">Montant (€)</Label>
                                    <Input
                                        id="amount"
                                        v-model="contributionForm.amount"
                                        type="number"
                                        min="1"
                                        step="0.01"
                                        placeholder="25"
                                        required
                                    />
                                    <InputError :message="contributionForm.errors.amount" />
                                </div>
                                <div class="space-y-2">
                                    <Label for="message">Message (optionnel)</Label>
                                    <textarea
                                        id="message"
                                        v-model="contributionForm.message"
                                        rows="3"
                                        class="flex w-full rounded-md border bg-background px-3 py-2 text-sm"
                                        placeholder="Un mot d'encouragement..."
                                    />
                                    <InputError :message="contributionForm.errors.message" />
                                </div>
                                <div class="flex items-center gap-2">
                                    <Checkbox
                                        id="is_anonymous"
                                        :checked="contributionForm.is_anonymous"
                                        @update:checked="(val: boolean) => contributionForm.is_anonymous = val"
                                    />
                                    <Label for="is_anonymous" class="text-sm font-normal">
                                        Contribuer anonymement
                                    </Label>
                                </div>
                                <InputError :message="contributionForm.errors.is_anonymous" />
                            </CardContent>
                            <CardFooter>
                                <Button type="submit" class="w-full" :disabled="contributionForm.processing">
                                    {{ contributionForm.processing ? 'En cours...' : 'Contribuer' }}
                                </Button>
                            </CardFooter>
                        </form>
                    </Card>

                    <Card v-else>
                        <CardContent class="p-6 text-center text-sm text-muted-foreground">
                            <template v-if="campaign.status === 'funded'">
                                🎉 Cette campagne a atteint son objectif !
                            </template>
                            <template v-else-if="campaign.status === 'closed'">
                                Cette campagne est clôturée.
                            </template>
                            <template v-else>
                                Cette campagne n'est pas encore active.
                            </template>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>

        <!-- Delete dialog -->
        <Dialog v-model:open="showDeleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Supprimer la campagne</DialogTitle>
                    <DialogDescription>
                        Êtes-vous sûr de vouloir supprimer « {{ campaign.title }} » ?
                        Toutes les contributions associées seront également supprimées.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="showDeleteDialog = false">Annuler</Button>
                    <Button variant="destructive" @click="deleteCampaign">Supprimer</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
