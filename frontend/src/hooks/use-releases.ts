import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import * as releasesService from '@/services/releases';
import type { CreateReleasePayload, UpdateReleasePayload } from '@/types/release';

export function useReleases(params?: { status?: string; search?: string; page?: number }) {
  return useQuery({
    queryKey: ['releases', params],
    queryFn: () => releasesService.fetchReleases(params),
  });
}

export function useRelease(key: string) {
  return useQuery({
    queryKey: ['releases', key],
    queryFn: () => releasesService.fetchRelease(key),
    enabled: !!key,
  });
}

export function useReleaseMetrics() {
  return useQuery({
    queryKey: ['releases', 'metrics'],
    queryFn: releasesService.fetchReleaseMetrics,
  });
}

export function useReleaseTracks(releaseKey: string) {
  return useQuery({
    queryKey: ['releases', releaseKey, 'tracks'],
    queryFn: () => releasesService.fetchReleaseTracks(releaseKey),
    enabled: !!releaseKey,
  });
}

export function useCreateRelease() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (payload: CreateReleasePayload) => releasesService.createRelease(payload),
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: ['releases'] });
    },
  });
}

export function useUpdateRelease() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: ({ key, payload }: { key: string; payload: UpdateReleasePayload }) =>
      releasesService.updateRelease(key, payload),
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: ['releases'] });
    },
  });
}

export function useDeleteRelease() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (key: string) => releasesService.deleteRelease(key),
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: ['releases'] });
    },
  });
}

export function useSubmitRelease() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (key: string) => releasesService.submitRelease(key),
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: ['releases'] });
    },
  });
}

export function useUploadTrack() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: ({
      releaseKey,
      file,
      position,
    }: {
      releaseKey: string;
      file: File;
      position: number;
    }) => releasesService.uploadTrack(releaseKey, file, position),
    onSuccess: (_data, variables) => {
      void queryClient.invalidateQueries({
        queryKey: ['releases', variables.releaseKey, 'tracks'],
      });
    },
  });
}

export function useDeleteTrack() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: ({ releaseKey, trackKey }: { releaseKey: string; trackKey: string }) =>
      releasesService.deleteTrack(releaseKey, trackKey),
    onSuccess: (_data, variables) => {
      void queryClient.invalidateQueries({
        queryKey: ['releases', variables.releaseKey, 'tracks'],
      });
    },
  });
}

export function useUploadCover() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: ({ releaseKey, file }: { releaseKey: string; file: File }) =>
      releasesService.uploadCover(releaseKey, file),
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: ['releases'] });
    },
  });
}
