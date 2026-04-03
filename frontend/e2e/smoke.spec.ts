import { test, expect } from '@playwright/test';

// Helpers
const API_URL = 'http://future-label.test/api/v1';

test.describe('Landing Pages', () => {
  test('landing page loads', async ({ page }) => {
    await page.goto('/');
    await expect(page.getByRole('heading', { name: 'Выпусти свою музыку' })).toBeVisible();
  });

  test('pricing page loads', async ({ page }) => {
    await page.goto('/pricing');
    await expect(page.getByRole('heading', { name: 'Тарифы' })).toBeVisible();
  });

  test('about page loads', async ({ page }) => {
    await page.goto('/about');
    await expect(page.getByRole('heading', { name: /Музыка, которая/ })).toBeVisible();
  });
});

test.describe('Auth Flow', () => {
  test('registration form works', async ({ page }) => {
    await page.goto('/register');
    await page.getByRole('textbox', { name: 'Name' }).fill('Test E2E User');
    await page.getByRole('textbox', { name: 'Email' }).fill(`e2e-${Date.now()}@test.com`);
    await page.getByRole('textbox', { name: /^Password$/ }).fill('TestPass123!');
    await page.getByRole('textbox', { name: 'Confirm' }).fill('TestPass123!');
    await page.getByRole('button', { name: 'Create account' }).click();
    await expect(page).toHaveURL(/\/login/);
    await expect(page.locator('text=Registration successful')).toBeVisible();
  });

  test('login as artist → dashboard', async ({ page }) => {
    await page.goto('/login');
    await page.getByRole('textbox', { name: 'Email' }).fill('danp@example.com');
    await page.getByRole('textbox', { name: 'Password' }).fill('password');
    await page.getByRole('button', { name: 'Sign in' }).click();
    await expect(page).toHaveURL(/\/dashboard/);
    await expect(page.getByRole('heading', { name: 'Дашборд' })).toBeVisible();
  });

  test('login as admin → admin panel', async ({ page }) => {
    await page.goto('/login');
    await page.getByRole('textbox', { name: 'Email' }).fill('admin@future-label.com');
    await page.getByRole('textbox', { name: 'Password' }).fill('password');
    await page.getByRole('button', { name: 'Sign in' }).click();
    await expect(page).toHaveURL(/\/admin/);
    await expect(page.getByRole('heading', { name: 'Панель администратора' })).toBeVisible();
  });

  test('forgot password shows confirmation', async ({ page }) => {
    await page.goto('/forgot-password');
    await page.getByRole('textbox', { name: 'Email' }).fill('danp@example.com');
    await page.getByRole('button', { name: 'Send reset link' }).click();
    await expect(page.locator('text=password reset link')).toBeVisible();
  });

  test('logout works', async ({ page }) => {
    // Login first
    await page.goto('/login');
    await page.getByRole('textbox', { name: 'Email' }).fill('danp@example.com');
    await page.getByRole('textbox', { name: 'Password' }).fill('password');
    await page.getByRole('button', { name: 'Sign in' }).click();
    await expect(page).toHaveURL(/\/dashboard/);
    // Logout
    await page.getByRole('button', { name: /Выход/ }).click();
    // Wait for token to be cleared from localStorage
    await page.waitForFunction(() => !localStorage.getItem('auth_token'), null, { timeout: 5000 });
    // Full page reload — the route guard should redirect to /login
    await page.reload();
    await expect(page).toHaveURL(/\/login/, { timeout: 10000 });
  });
});

test.describe('Artist Dashboard', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.getByRole('textbox', { name: 'Email' }).fill('danp@example.com');
    await page.getByRole('textbox', { name: 'Password' }).fill('password');
    await page.getByRole('button', { name: 'Sign in' }).click();
    await expect(page).toHaveURL(/\/dashboard/);
  });

  test('dashboard shows releases list', async ({ page }) => {
    await expect(page.getByRole('heading', { name: 'Дашборд' })).toBeVisible();
    await expect(page.getByText(/Релизы \(\d+\)/)).toBeVisible();
  });

  test('new release wizard step 1', async ({ page }) => {
    await page.getByRole('button', { name: /Новый релиз/ }).first().click();
    await expect(page).toHaveURL(/\/releases\/new/);
    await expect(page.getByText('Тип релиза').first()).toBeVisible();
    // Fill step 1
    await page.getByLabel('Название').fill('E2E Тест Релиз');
    await page.getByLabel('Артист').fill('E2E Artist');
    // Next should be enabled
    await expect(page.getByRole('button', { name: 'Далее' })).toBeEnabled();
  });

  test('contracts page loads', async ({ page }) => {
    await page.getByRole('link', { name: 'Контракты' }).click();
    await expect(page).toHaveURL(/\/contracts/);
  });
});

test.describe('Admin Panel', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.getByRole('textbox', { name: 'Email' }).fill('admin@future-label.com');
    await page.getByRole('textbox', { name: 'Password' }).fill('password');
    await page.getByRole('button', { name: 'Sign in' }).click();
    await expect(page).toHaveURL(/\/admin/);
  });

  test('admin dashboard shows metrics', async ({ page }) => {
    await expect(page.getByRole('heading', { name: 'Панель администратора' })).toBeVisible();
    await expect(page.getByRole('main').getByText('Артисты')).toBeVisible();
  });

  test('admin artists page shows list', async ({ page }) => {
    await page.getByRole('link', { name: 'Артисты' }).click();
    await expect(page).toHaveURL(/\/admin\/artists/);
    await expect(page.getByText(/Артисты \(\d+\)/)).toBeVisible();
  });

  test('admin services page loads', async ({ page }) => {
    await page.getByRole('link', { name: 'Сервисы' }).click();
    await expect(page).toHaveURL(/\/admin\/services/);
  });

  test('admin releases page loads', async ({ page }) => {
    await page.getByRole('link', { name: 'Релизы' }).click();
    await expect(page).toHaveURL(/\/admin\/releases/);
  });
});
