<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';

type Category = {
    id: number;
    name: string;
};

type Campaign = {
    id: number;
    title: string;
    description: string;
    goal_amount: string;
    start_date: string;
    end_date: string;
    status: string;
    category_id: number | null;
    image: string | null;
};

const props = defineProps<{
    campaign: Campaign;
    categories: Category[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Tableau de bord', href: '/dashboard' },
    { title: 'Campagnes', href: '/campaigns' },
    { title: 'Modifier' },
];

const form = useForm({
    _method: 'put' as const,
    category_id: props.campaign.category_id ?? '',
    title: props.campaign.title,
    description: props.campaign.description,
    goal_amount: props.campaign.goal_amount,
    start_date: props.campaign.start_date?.substring(0, 10) ?? '',
    end_date: props.campaign.end_date?.substring(0, 10) ?? '',
    status: props.campaign.status,
    image: null as File | null,
});

function submit() {
    form.post(`/campaigns/${props.campaign.id}`, {
        preserveScroll: true,
        forceFormData: true,
    });
}
</script>

<template>
    <Head :title="`Modifier ${campaign.title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Heading :title="`Modifier « ${campaign.title} »`" description="Modifiez les informations de votre campagne." />

            <Card class="mx-auto w-full max-w-2xl">
                <form @submit.prevent="submit">
                    <CardHeader>
                        <CardTitle>Informations de la campagne</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-2">
                            <Label for="title">Titre</Label>
                            <Input id="title" v-model="form.title" required />
                            <InputError :message="form.errors.title" />
                        </div>

                        <div class="space-y-2">
                            <Label for="category_id">Catégorie</Label>
                            <select
                                id="category_id"
                                v-model="form.category_id"
                                class="flex h-9 w-full rounded-md border bg-background px-3 py-1 text-sm"
                            >
                                <option value="">Aucune catégorie</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.category_id" />
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="5"
                                class="flex w-full rounded-md border bg-background px-3 py-2 text-sm"
                                required
                            />
                            <InputError :message="form.errors.description" />
                        </div>

                        <div class="space-y-2">
                            <Label for="goal_amount">Objectif (€)</Label>
                            <Input
                                id="goal_amount"
                                v-model="form.goal_amount"
                                type="number"
                                min="1"
                                step="0.01"
                                required
                            />
                            <InputError :message="form.errors.goal_amount" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="start_date">Date de début</Label>
                                <Input id="start_date" v-model="form.start_date" type="date" required />
                                <InputError :message="form.errors.start_date" />
                            </div>
                            <div class="space-y-2">
                                <Label for="end_date">Date de fin</Label>
                                <Input id="end_date" v-model="form.end_date" type="date" required />
                                <InputError :message="form.errors.end_date" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="status">Statut</Label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="flex h-9 w-full rounded-md border bg-background px-3 py-1 text-sm"
                            >
                                <option value="draft">Brouillon</option>
                                <option value="active">Active</option>
                                <option value="funded">Financée</option>
                                <option value="closed">Clôturée</option>
                            </select>
                            <InputError :message="form.errors.status" />
                        </div>

                        <div class="space-y-2">
                            <Label for="image">Image (laisser vide pour conserver l'actuelle)</Label>
                            <Input
                                id="image"
                                type="file"
                                accept="image/*"
                                @change="(e: Event) => form.image = (e.target as HTMLInputElement).files?.[0] ?? null"
                            />
                            <InputError :message="form.errors.image" />
                        </div>
                    </CardContent>
                    <CardFooter class="flex justify-end gap-2">
                        <Link :href="`/campaigns/${campaign.id}`">
                            <Button type="button" variant="outline">Annuler</Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Mise à jour...' : 'Mettre à jour' }}
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
