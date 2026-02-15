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

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Tableau de bord', href: '/dashboard' },
    { title: 'Catégories', href: '/categories' },
    { title: 'Créer' },
];

const form = useForm({
    name: '',
    icon: '',
});

function submit() {
    form.post('/categories', {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Nouvelle catégorie" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Heading title="Nouvelle catégorie" description="Créez une nouvelle catégorie de campagne." />

            <Card class="mx-auto w-full max-w-lg">
                <form @submit.prevent="submit">
                    <CardHeader>
                        <CardTitle>Informations</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-2">
                            <Label for="name">Nom</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                placeholder="Ex : Technologie, Santé, Éducation..."
                                required
                            />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="space-y-2">
                            <Label for="icon">Icône (emoji)</Label>
                            <Input
                                id="icon"
                                v-model="form.icon"
                                placeholder="Ex : 🚀, 🏥, 📚..."
                            />
                            <InputError :message="form.errors.icon" />
                        </div>
                    </CardContent>
                    <CardFooter class="flex justify-end gap-2">
                        <Link href="/categories">
                            <Button type="button" variant="outline">Annuler</Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Création...' : 'Créer' }}
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
