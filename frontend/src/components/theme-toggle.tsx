import { Moon, Sun } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { useTheme } from '@/lib/theme';

export function ThemeToggle() {
  const { theme, setTheme } = useTheme();

  const isDark =
    theme === 'dark' ||
    (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

  return (
    <Button
      variant="ghost"
      size="sm"
      onClick={() => setTheme(isDark ? 'light' : 'dark')}
      className="h-8 w-8 p-0"
      aria-label="Toggle theme"
    >
      {isDark ? <Sun className="h-4 w-4" /> : <Moon className="h-4 w-4" />}
    </Button>
  );
}
