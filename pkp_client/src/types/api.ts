// types/api.ts
export interface CatchmentOverviewProps {
  title: string;
  value: string | number;
  subtitle: string;
}

export interface Site {
  code: string;
  name: string;
  status: string;
  latitude?: number;       // Optional for map integration
  longitude?: number;      // Optional for map integration
}

export interface Coordinates {
  latitude: number;
  longitude: number;
}

export interface BarangaySite {
  barangay_id: number;
  barangay_name: string;
  total_no_sitio_purok: number;
  total_target_sitio_purok: number;
  latitude: number;
  longitude: number;
}

export interface Municipality {
  municipality_id: number;
  municipality_name: string;
  total_no_sitio_purok: number;
  total_target_sitio_purok: number;
  sites: BarangaySite[];
}

export interface ProvinceData {
  province_id: number;
  province_name: string;
  total_no_sitio_purok: number;
  total_target_sitio_purok: number;
  municipalities: Municipality[];
}

export interface GrandTotals {
  total_no_sitio_purok: number;
  total_target_sitio_purok: number;
  site_count: number;
}

export interface ApiResponse {
  message: string;
  grand_totals: GrandTotals;
  data: ProvinceData[];
}

export interface ProvincePayload {
  name: string;
  info: Record<string, unknown>;
}

export interface MarkerPayload {
  barangay_id: number;
  barangay_name: string;
  municipality_id: number;
  municipality_name: string;
  province_id: number;
  province_name: string;
  title: string;
  population?: number;
  region: string;
  level?: "province" | "municipality" | "barangay";
  status?: string;
  latitude?: number;
  longitude?: number;
  total_no_sitio_purok?: number;
  total_target_sitio_purok?: number;

}
