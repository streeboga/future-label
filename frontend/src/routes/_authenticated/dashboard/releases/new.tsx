import { createFileRoute, useNavigate } from '@tanstack/react-router';
import { useState, useCallback, useRef } from 'react';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useCreateRelease, useUpdateRelease, useUploadTrack, useSubmitRelease } from '@/hooks/use-releases';
import { useServices } from '@/hooks/use-services';
import type { ReleaseType, Release, Track } from '@/types/release';
import type { Service } from '@/types/service';
import {
  Disc3,
  Music,
  Album,
  ArrowLeft,
  ArrowRight,
  Check,
  Upload,
  X,
  GripVertical,
  Image,
  Loader2,
} from 'lucide-react';

export const Route = createFileRoute('/_authenticated/dashboard/releases/new')({
  component: ReleaseWizard,
});

type WizardStep = 1 | 2 | 3 | 4 | 5;

const steps = [
  { num: 1, label: 'Тип релиза' },
  { num: 2, label: 'Треки' },
  { num: 3, label: 'Обложка' },
  { num: 4, label: 'Сервисы' },
  { num: 5, label: 'Проверка' },
];

const typeOptions: { value: ReleaseType; label: string; desc: string; icon: typeof Disc3 }[] = [
  { value: 'single', label: 'Сингл', desc: '1 трек', icon: Music },
  { value: 'ep', label: 'EP', desc: '2-6 треков', icon: Disc3 },
  { value: 'album', label: 'Альбом', desc: '7+ треков', icon: Album },
];

function ReleaseWizard() {
  const navigate = useNavigate();
  const [step, setStep] = useState<WizardStep>(1);
  const [release, setRelease] = useState<Release | null>(null);
  const [tracks, setTracks] = useState<Track[]>([]);
  const [selectedServices, setSelectedServices] = useState<string[]>([]);
  const [isSaving, setIsSaving] = useState(false);

  // Step 1 fields
  const [releaseType, setReleaseType] = useState<ReleaseType>('single');
  const [title, setTitle] = useState('');
  const [artistName, setArtistName] = useState('');

  // Step 3 fields
  const [genre, setGenre] = useState('');
  const [language, setLanguage] = useState('');
  const [releaseDate, setReleaseDate] = useState('');
  const [description, setDescription] = useState('');
  const [coverPreview, setCoverPreview] = useState<string | null>(null);

  const fileInputRef = useRef<HTMLInputElement>(null);
  const coverInputRef = useRef<HTMLInputElement>(null);

  const createRelease = useCreateRelease();
  const updateRelease = useUpdateRelease();
  const uploadTrack = useUploadTrack();
  const submitRelease = useSubmitRelease();
  const { data: services = [] } = useServices();

  const handleNext = useCallback(async () => {
    setIsSaving(true);
    try {
      if (step === 1) {
        if (!release) {
          const created = await createRelease.mutateAsync({
            title,
            artist_name: artistName,
            type: releaseType,
          });
          setRelease(created);
        } else {
          const updated = await updateRelease.mutateAsync({
            key: release.key,
            payload: { title, artist_name: artistName, type: releaseType },
          });
          setRelease(updated);
        }
      } else if (step === 3 && release) {
        await updateRelease.mutateAsync({
          key: release.key,
          payload: {
            genre: genre || null,
            language: language || null,
            release_date: releaseDate || null,
            description: description || null,
          },
        });
      }
      setStep((s) => Math.min(s + 1, 5) as WizardStep);
    } catch {
      // Error handled by mutation
    } finally {
      setIsSaving(false);
    }
  }, [step, release, title, artistName, releaseType, genre, language, releaseDate, description, createRelease, updateRelease]);

  const handleBack = () => {
    setStep((s) => Math.max(s - 1, 1) as WizardStep);
  };

  const handleFileUpload = async (files: FileList | null) => {
    if (!files || !release) return;
    for (let i = 0; i < files.length; i++) {
      const file = files[i];
      if (!file) continue;
      try {
        const track = await uploadTrack.mutateAsync({
          releaseKey: release.key,
          file,
          position: tracks.length + i + 1,
        });
        setTracks((prev) => [...prev, track]);
      } catch {
        // Error handled by mutation
      }
    }
  };

  const handleCoverUpload = (file: File) => {
    setCoverPreview(URL.createObjectURL(file));
  };

  const handleRemoveTrack = (index: number) => {
    setTracks((prev) => prev.filter((_, i) => i !== index));
  };

  const handleSubmit = async () => {
    if (!release) return;
    setIsSaving(true);
    try {
      await submitRelease.mutateAsync(release.key);
      void navigate({ to: '/dashboard/releases/$key', params: { key: release.key } });
    } catch {
      // Error handled by mutation
    } finally {
      setIsSaving(false);
    }
  };

  const toggleService = (key: string) => {
    setSelectedServices((prev) =>
      prev.includes(key) ? prev.filter((k) => k !== key) : [...prev, key]
    );
  };

  const canProceed = () => {
    if (step === 1) return title.trim() !== '' && artistName.trim() !== '';
    return true;
  };

  const formatFileSize = (bytes: number | null) => {
    if (!bytes) return '--';
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(0)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
  };

  return (
    <div className="mx-auto max-w-2xl space-y-6">
      {/* Step indicator */}
      <div className="flex items-center gap-1">
        {steps.map((s, i) => (
          <div key={s.num} className="flex flex-1 items-center">
            <div className="flex flex-1 flex-col items-center">
              <div
                className={`flex h-8 w-8 items-center justify-center rounded-full text-xs font-medium transition-colors ${
                  step >= s.num
                    ? 'bg-primary text-primary-foreground'
                    : 'bg-muted text-muted-foreground'
                }`}
              >
                {step > s.num ? <Check className="h-4 w-4" /> : s.num}
              </div>
              <span className="mt-1 hidden text-[10px] text-muted-foreground sm:block">
                {s.label}
              </span>
            </div>
            {i < steps.length - 1 && (
              <div
                className={`mx-1 h-px flex-1 transition-colors ${
                  step > s.num ? 'bg-primary' : 'bg-border'
                }`}
              />
            )}
          </div>
        ))}
      </div>

      {/* Step content */}
      {step === 1 && (
        <Card>
          <CardHeader>
            <CardTitle>Тип релиза</CardTitle>
            <CardDescription>Выберите тип и заполните основную информацию</CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-3 gap-3">
              {typeOptions.map((opt) => (
                <button
                  key={opt.value}
                  type="button"
                  onClick={() => setReleaseType(opt.value)}
                  className={`flex flex-col items-center gap-2 rounded-lg border-2 p-4 transition-colors ${
                    releaseType === opt.value
                      ? 'border-primary bg-primary/5'
                      : 'border-border hover:border-muted-foreground/30'
                  }`}
                >
                  <opt.icon
                    className={`h-6 w-6 ${
                      releaseType === opt.value ? 'text-primary' : 'text-muted-foreground'
                    }`}
                  />
                  <span className="text-sm font-medium">{opt.label}</span>
                  <span className="text-xs text-muted-foreground">{opt.desc}</span>
                </button>
              ))}
            </div>
            <div className="space-y-2">
              <Label htmlFor="title">Название</Label>
              <Input
                id="title"
                value={title}
                onChange={(e) => setTitle(e.target.value)}
                placeholder="Название релиза"
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="artist">Артист</Label>
              <Input
                id="artist"
                value={artistName}
                onChange={(e) => setArtistName(e.target.value)}
                placeholder="Имя артиста"
              />
            </div>
          </CardContent>
        </Card>
      )}

      {step === 2 && (
        <Card>
          <CardHeader>
            <CardTitle>Треки</CardTitle>
            <CardDescription>Загрузите аудиофайлы для вашего релиза</CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            {/* Drag and drop zone */}
            <div
              className="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-border p-8 transition-colors hover:border-primary/50 hover:bg-muted/30"
              onClick={() => fileInputRef.current?.click()}
              onDragOver={(e) => {
                e.preventDefault();
                e.stopPropagation();
              }}
              onDrop={(e) => {
                e.preventDefault();
                e.stopPropagation();
                void handleFileUpload(e.dataTransfer.files);
              }}
            >
              <Upload className="mb-2 h-8 w-8 text-muted-foreground/50" />
              <p className="text-sm font-medium text-muted-foreground">
                Перетащите файлы сюда или нажмите для выбора
              </p>
              <p className="text-xs text-muted-foreground/70">MP3, WAV, FLAC</p>
              <input
                ref={fileInputRef}
                type="file"
                accept="audio/*"
                multiple
                className="hidden"
                onChange={(e) => void handleFileUpload(e.target.files)}
              />
            </div>

            {uploadTrack.isPending && (
              <div className="flex items-center gap-2 text-sm text-muted-foreground">
                <Loader2 className="h-4 w-4 animate-spin" />
                Загрузка...
              </div>
            )}

            {/* Track list */}
            {tracks.length > 0 && (
              <div className="space-y-1">
                {tracks.map((track, index) => (
                  <div
                    key={track.key}
                    className="flex items-center gap-3 rounded-md border px-3 py-2"
                  >
                    <GripVertical className="h-4 w-4 shrink-0 text-muted-foreground/40" />
                    <span className="w-6 text-center text-xs text-muted-foreground">
                      {index + 1}
                    </span>
                    <div className="flex-1 overflow-hidden">
                      <p className="truncate text-sm font-medium">
                        {track.title || `Трек ${index + 1}`}
                      </p>
                      <p className="text-xs text-muted-foreground">
                        {track.format?.toUpperCase() ?? '--'} &middot;{' '}
                        {formatFileSize(track.file_size)}
                      </p>
                    </div>
                    <button
                      onClick={() => handleRemoveTrack(index)}
                      className="rounded p-1 text-muted-foreground hover:bg-muted hover:text-foreground"
                    >
                      <X className="h-3.5 w-3.5" />
                    </button>
                  </div>
                ))}
              </div>
            )}
          </CardContent>
        </Card>
      )}

      {step === 3 && (
        <Card>
          <CardHeader>
            <CardTitle>Обложка и метаданные</CardTitle>
            <CardDescription>Загрузите обложку и заполните дополнительную информацию</CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            {/* Cover upload */}
            <div className="flex items-start gap-4">
              <div
                className="flex h-32 w-32 shrink-0 cursor-pointer items-center justify-center rounded-lg border-2 border-dashed bg-muted/30 transition-colors hover:border-primary/50"
                onClick={() => coverInputRef.current?.click()}
              >
                {coverPreview || release?.cover_url ? (
                  <img
                    src={coverPreview ?? release?.cover_url ?? ''}
                    alt="Cover"
                    className="h-full w-full rounded-lg object-cover"
                  />
                ) : (
                  <div className="flex flex-col items-center gap-1">
                    <Image className="h-6 w-6 text-muted-foreground/50" />
                    <span className="text-[10px] text-muted-foreground">Обложка</span>
                  </div>
                )}
                <input
                  ref={coverInputRef}
                  type="file"
                  accept="image/*"
                  className="hidden"
                  onChange={(e) => {
                    const file = e.target.files?.[0];
                    if (file) handleCoverUpload(file);
                  }}
                />
              </div>
              <div className="flex-1 space-y-3">
                <div className="space-y-1.5">
                  <Label htmlFor="genre">Жанр</Label>
                  <Input
                    id="genre"
                    value={genre}
                    onChange={(e) => setGenre(e.target.value)}
                    placeholder="Pop, Rock, Hip-Hop..."
                  />
                </div>
                <div className="space-y-1.5">
                  <Label htmlFor="language">Язык</Label>
                  <Input
                    id="language"
                    value={language}
                    onChange={(e) => setLanguage(e.target.value)}
                    placeholder="Русский, English..."
                  />
                </div>
              </div>
            </div>
            <div className="space-y-1.5">
              <Label htmlFor="releaseDate">Дата релиза</Label>
              <Input
                id="releaseDate"
                type="date"
                value={releaseDate}
                onChange={(e) => setReleaseDate(e.target.value)}
              />
            </div>
            <div className="space-y-1.5">
              <Label htmlFor="description">Описание</Label>
              <Textarea
                id="description"
                value={description}
                onChange={(e) => setDescription(e.target.value)}
                placeholder="Расскажите о релизе..."
                rows={3}
              />
            </div>
          </CardContent>
        </Card>
      )}

      {step === 4 && (
        <Card>
          <CardHeader>
            <CardTitle>Дополнительные сервисы</CardTitle>
            <CardDescription>Выберите услуги для продвижения вашего релиза</CardDescription>
          </CardHeader>
          <CardContent>
            {services.length === 0 ? (
              <p className="py-8 text-center text-sm text-muted-foreground">
                Нет доступных сервисов
              </p>
            ) : (
              <div className="grid gap-3 sm:grid-cols-2">
                {services
                  .filter((s: Service) => s.is_active)
                  .map((service: Service) => {
                    const isSelected = selectedServices.includes(service.key);
                    return (
                      <button
                        key={service.key}
                        type="button"
                        onClick={() => toggleService(service.key)}
                        className={`flex flex-col items-start rounded-lg border-2 p-4 text-left transition-colors ${
                          isSelected
                            ? 'border-primary bg-primary/5'
                            : 'border-border hover:border-muted-foreground/30'
                        }`}
                      >
                        <div className="flex w-full items-start justify-between">
                          <span className="text-sm font-medium">{service.title}</span>
                          <span className="text-sm font-semibold text-primary">
                            {service.price.toLocaleString('ru-RU')} {service.currency}
                          </span>
                        </div>
                        {service.description && (
                          <p className="mt-1 text-xs text-muted-foreground line-clamp-2">
                            {service.description}
                          </p>
                        )}
                        <span className="mt-2 inline-block rounded bg-muted px-1.5 py-0.5 text-[10px] text-muted-foreground">
                          {service.category}
                        </span>
                      </button>
                    );
                  })}
              </div>
            )}
          </CardContent>
        </Card>
      )}

      {step === 5 && (
        <Card>
          <CardHeader>
            <CardTitle>Проверка и отправка</CardTitle>
            <CardDescription>Проверьте данные перед отправкой на модерацию</CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid gap-3 sm:grid-cols-2">
              <SummaryItem label="Название" value={release?.title ?? title} />
              <SummaryItem label="Артист" value={release?.artist_name ?? artistName} />
              <SummaryItem label="Тип" value={releaseType} />
              <SummaryItem label="Треков" value={String(tracks.length)} />
              <SummaryItem label="Жанр" value={genre || '--'} />
              <SummaryItem label="Язык" value={language || '--'} />
              <SummaryItem label="Дата релиза" value={releaseDate || '--'} />
              <SummaryItem
                label="Сервисы"
                value={selectedServices.length > 0 ? `${selectedServices.length} выбрано` : '--'}
              />
            </div>
            {description && (
              <div>
                <p className="mb-1 text-xs font-medium text-muted-foreground">Описание</p>
                <p className="text-sm">{description}</p>
              </div>
            )}
            {tracks.length > 0 && (
              <div>
                <p className="mb-2 text-xs font-medium text-muted-foreground">Трек-лист</p>
                <div className="space-y-1">
                  {tracks.map((track, i) => (
                    <div key={track.key} className="flex items-center gap-2 text-sm">
                      <span className="w-5 text-right text-muted-foreground">{i + 1}.</span>
                      <span>{track.title || `Трек ${i + 1}`}</span>
                    </div>
                  ))}
                </div>
              </div>
            )}
          </CardContent>
        </Card>
      )}

      {/* Navigation */}
      <div className="flex items-center justify-between">
        <Button variant="outline" onClick={handleBack} disabled={step === 1} className="gap-1.5">
          <ArrowLeft className="h-4 w-4" />
          Назад
        </Button>
        {step < 5 ? (
          <Button onClick={handleNext} disabled={!canProceed() || isSaving} className="gap-1.5">
            {isSaving ? (
              <Loader2 className="h-4 w-4 animate-spin" />
            ) : (
              <ArrowRight className="h-4 w-4" />
            )}
            Далее
          </Button>
        ) : (
          <Button onClick={handleSubmit} disabled={isSaving} className="gap-1.5">
            {isSaving ? (
              <Loader2 className="h-4 w-4 animate-spin" />
            ) : (
              <Check className="h-4 w-4" />
            )}
            Отправить на модерацию
          </Button>
        )}
      </div>
    </div>
  );
}

function SummaryItem({ label, value }: { label: string; value: string }) {
  return (
    <div>
      <p className="text-xs font-medium text-muted-foreground">{label}</p>
      <p className="text-sm font-medium">{value}</p>
    </div>
  );
}
