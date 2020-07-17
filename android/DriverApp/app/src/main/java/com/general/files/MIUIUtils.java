package com.general.files;

import android.app.AppOpsManager;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.os.Binder;
import android.provider.Settings;

import java.io.IOException;
import java.lang.reflect.Method;

/**
 * Created by Admin on 13-09-2017.
 */

public class MIUIUtils {
    private static final String MIUI_V5 = "V5";
    private static final String MIUI_V6 = "V6";

    private static final String KEY_MIUI_VERSION_CODE = "ro.miui.ui.version.code";
    private static final String KEY_MIUI_VERSION_NAME = "ro.miui.ui.version.name";
    private static final String KEY_MIUI_INTERNAL_STORAGE = "ro.miui.internal.storage";

    public static boolean isMIUI() {
        try {
            final BuildProperties prop = BuildProperties.newInstance();
            return prop.getProperty(KEY_MIUI_VERSION_CODE, null) != null
                    || prop.getProperty(KEY_MIUI_VERSION_NAME, null) != null
                    || prop.getProperty(KEY_MIUI_INTERNAL_STORAGE, null) != null;
        } catch (IOException e) {
            return false;
        }
    }

    public static boolean isMIUIV5() {
        return getVersionName().equals(MIUI_V5);
    }

    public static boolean isMIUIV6() {
        return getVersionName().equals(MIUI_V6);
    }

    public static String getVersionName() {
        try {
            final BuildProperties prop = BuildProperties.newInstance();
            return prop.getProperty(KEY_MIUI_VERSION_NAME);
        } catch (IOException e) {
            return "";
        }
    }

    public static boolean isFloatWindowOptionAllowed(Context context) {
        AppOpsManager manager = (AppOpsManager) context.getSystemService(Context.APP_OPS_SERVICE);
        Class localClass = manager.getClass();
        Class[] arrayOfClass = new Class[3];
        arrayOfClass[0] = Integer.TYPE;
        arrayOfClass[1] = Integer.TYPE;
        arrayOfClass[2] = String.class;
        try {
            Method method = localClass.getMethod("checkOp", arrayOfClass);
            if (method == null) {
                return false;
            }
            Object[] arrayOfObjects = new Object[3];
            arrayOfObjects[0] = Integer.valueOf(24);
            arrayOfObjects[1] = Integer.valueOf(Binder.getCallingUid());
            arrayOfObjects[2] = context.getPackageName();
            int m = ((Integer) method.invoke((Object) manager, arrayOfObjects)).intValue();
            return m == AppOpsManager.MODE_ALLOWED;
        } catch (Exception e) {
            return false;
        }
    }

    public static Intent toPermissionManager(Context context, String packageName) {
        Intent intent = new Intent("miui.intent.action.APP_PERM_EDITOR");
        String version = getVersionName();
        if (MIUI_V5.equals(version)) {
            PackageInfo pInfo;
            try {
                pInfo = context.getPackageManager().getPackageInfo(packageName, 0);
            } catch (PackageManager.NameNotFoundException ignored) {
                return null;
            }
            intent.setClassName("com.android.settings", "com.miui.securitycenter.permission.AppPermissionsEditor");
            intent.putExtra("extra_package_uid", pInfo.applicationInfo.uid);
        } else { // MIUI_V6 and above
            final String PKG_SECURITY_CENTER = "com.miui.securitycenter";
            try {
                context.getPackageManager().getPackageInfo(PKG_SECURITY_CENTER, PackageManager.GET_ACTIVITIES);
            } catch (PackageManager.NameNotFoundException ignored) {
                return null;
            }
            intent.setClassName(PKG_SECURITY_CENTER, "com.miui.permcenter.permissions.AppPermissionsEditorActivity");
            intent.putExtra("extra_pkgname", packageName);
        }
        return intent;
    }


    public static Intent toFloatWindowPermission(Context context, String packageName) {
        Uri packageUri = Uri.parse("package:" + packageName);
        Intent detailsIntent = new Intent(Settings.ACTION_APPLICATION_DETAILS_SETTINGS, packageUri);
        detailsIntent.addCategory(Intent.CATEGORY_DEFAULT);
        if (isMIUIV5()) {
            return detailsIntent;
        } else {
            Intent permIntent = toPermissionManager(context, packageName);
            return permIntent == null ? detailsIntent : permIntent;
        }
    }
}