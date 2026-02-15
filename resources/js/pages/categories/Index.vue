<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { type BreadcrumbItem } from '@/types';
import { ref } from 'vue';

type Category = {
    id: number;
    name: string;
    slug: string;
    icon: string | null;
    campaigns_count: number;
};

type PaginatedData<T> = {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
};

defineProps<{
    categories: PaginatedData<Category>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Tableau de bord', href: '/dashboard' },
    { title: 'Catégories', href: '/categories' },
];

const page = usePage();
const flash = page.props.flash as { success?: string } | undefined;

const showDeleteDialog = ref(false);
const categoryToDelete = ref<Category | null>(null);

function confirmDelete(category: Category) {
    categoryToDelete.value = category;
    showDeleteDialog.value = true;
}

function deleteCategory() {
    if (categoryToDelete.value) {
        router.delete(`/categories/${categoryToDelete.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                showDeleteDialog.value = false;
                categoryToDelete.value = null;
            },
        });
    }
}
</script>

<template>
    <Head title="Catégories" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <Heading title="Catégories" description="Gérez les catégories de campagnes." />
                <Link href="/categories/create">
                    <Button>
                        <Plus class="size-4" />
                        Nouvelle catégorie
                    </Button>
                </Link>
            </div>

            <!-- Flash message -->
            <div
                v-if="flash?.success"
                class="rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-950 dark:text-green-200"
            >
                {{ flash.success }}
            </div>

            <!-- Categories grid -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="category in categories.data" :key="category.id">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-lg font-medium">
                            {{ category.name }}
                        </CardTitle>
                        <Badge variant="secondary">
                            {{ category.campaigns_count }} campagne{{ category.campaigns_count > 1 ? 's' : '' }}
                        </Badge>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center justify-between">
                            <span v-if="category.icon" class="text-2xl">{{ category.icon }}</span>
                            <span v-else class="text-sm text-muted-foreground">Aucune icône</span>
                            <div class="flex gap-2">
                                <Link :href="`/categories/${category.id}/edit`">
                                    <Button variant="outline" size="icon-sm">
                                        <Pencil class="size-4" />
                                    </Button>
                                </Link>
                                <Button
                                    variant="destructive"
                                    size="icon-sm"
                                    @click="confirmDelete(category)"
                                >
                                    <Trash2 class="size-4" />
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Empty state -->
            <div
                v-if="categories.data.length === 0"
                class="flex flex-col items-center justify-center rounded-xl border border-dashed p-12 text-center"
            >
                <p class="text-muted-foreground">Aucune catégorie pour le moment.</p>
                <Link href="/categories/create" class="mt-4">
                    <Button variant="outline">
                        <Plus class="size-4" />
                        Créer une catégorie
                    </Button>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="categories.last_page > 1" class="flex justify-center gap-1">
                <template v-for="link in categories.links" :key="link.label">
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

        <!-- Delete confirmation dialog -->
        <Dialog v-model:open="showDeleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Supprimer la catégorie</DialogTitle>
                    <DialogDescription>
                        Êtes-vous sûr de vouloir supprimer la catégorie
                        « {{ categoryToDelete?.name }} » ? Cette action est irréversible.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="showDeleteDialog = false">Annuler</Button>
                    <Button variant="destructive" @click="deleteCategory">Supprimer</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
