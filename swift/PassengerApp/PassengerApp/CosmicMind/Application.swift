import UIKit

public struct Application {
    /// A reference to the main UIWindow.
    public static var keyWindow: UIWindow? {
        return UIApplication.shared.keyWindow
    }
    
    public static var window:UIWindow? {
        return UIApplication.shared.delegate!.window!
    }
    
    public static var screenSize:CGRect{
        return UIScreen.main.bounds
    }
    
    public static var statusBarHeight:CGFloat {
        return UIApplication.shared.statusBarFrame.height
    }
    
    public static var defaultStatusBarHeight:CGFloat {
        return 20
    }
    
    /// A Boolean indicating if the device is in Landscape mode.
    public static var isLandscape: Bool {
        return UIApplication.shared.statusBarOrientation.isLandscape
    }
    
    /// A Boolean indicating if the device is in Portrait mode.
    public static var isPortrait: Bool {
        return !isLandscape
    }
    
    /// The current UIInterfaceOrientation value.
    public static var orientation: UIInterfaceOrientation {
        return UIApplication.shared.statusBarOrientation
    }
    
    /// Retrieves the device status bar style.
    public static var statusBarStyle: UIStatusBarStyle {
        get {
            return UIApplication.shared.statusBarStyle
        }
        set(value) {
            UIApplication.shared.statusBarStyle = value
        }
    }
    
    /// Retrieves the device status bar hidden state.
    public static var isStatusBarHidden: Bool {
        get {
            return UIApplication.shared.isStatusBarHidden
        }
        set(value) {
            UIApplication.shared.isStatusBarHidden = value
        }
    }
    
    /**
     A boolean that indicates based on iPhone rules if the
     status bar should be shown.
     */
    public static var shouldStatusBarBeHidden: Bool {
        return isLandscape && .phone == Device.userInterfaceIdiom
    }
    
    /// A reference to the user interface layout direction.
    public static var userInterfaceLayoutDirection: UIUserInterfaceLayoutDirection {
        return UIApplication.shared.userInterfaceLayoutDirection
    }
}
