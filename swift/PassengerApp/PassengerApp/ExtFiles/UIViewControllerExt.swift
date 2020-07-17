//
//  UIViewControllerExt.swift
//  PassengerApp
//
//  Created by NEW MAC on 08/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import Foundation
extension UIViewController {
    
    
    func pushToNavController(uv:UIViewController){
        
        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        
        if (Application.window != nil)
        {
            Application.window?.endEditing(true)
        }
        else
        {
            uv.view.endEditing(true)
        }
        self.closeDrawerMenu()
        
        DispatchQueue.main.async() {
            
            if(self.navigationController == nil){
                let navController = UINavigationController(rootViewController: uv)
                navController.navigationBar.isTranslucent = false
                self.present(navController, animated: true, completion: nil)
            }else{
                self.navigationController?.pushViewController(uv, animated: true)
            }
        }
    }
    
    func pushToNavController(uv:UIViewController, isDirect:Bool){
        
        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        
        if (Application.window != nil)
        {
            Application.window?.endEditing(true)
        }
        else
        {
            uv.view.endEditing(true)
        }
        self.closeDrawerMenu()
        
        self.view.window?.endEditing(true)
        
        DispatchQueue.main.async() {
            let navController = UINavigationController(rootViewController: uv)
            navController.navigationBar.isTranslucent = false
            self.present(navController, animated: false, completion: nil)
        }
    }
    
    func addBackBarBtn(){
        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)

        var backImg = UIImage(named: "ic_nav_bar_back")!
        if(Configurations.isRTLMode()){
            backImg = backImg.imageRotatedByDegrees(oldImage: backImg, deg: 180)
        }
        
        let leftButton: UIBarButtonItem = UIBarButtonItem(image: backImg, style: UIBarButtonItemStyle.plain, target: self, action: #selector(self.closeCurrentScreen))
        self.navigationItem.leftBarButtonItem = leftButton;
    }
    
    func closeCurrentScreen(){
        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        
        if (Application.window != nil)
        {
            Application.window?.endEditing(true)
        }
        else
        {
            self.view.endEditing(true)
        }
        
        if(self.navigationController == nil || self.navigationController?.viewControllers.count == 1){
            self.dismiss(animated: true, completion: nil)
        }else{
            self.navigationController?.popViewController(animated: true)
        }
    }
    
    func addActivityIndicator() -> UIActivityIndicatorView{
        let loader_IV = UIActivityIndicatorView(frame: CGRect(x: 0, y: 0, width: 50, height: 50))
        
        loader_IV.hidesWhenStopped = false
        loader_IV.startAnimating()
        loader_IV.activityIndicatorViewStyle = UIActivityIndicatorViewStyle.whiteLarge
        loader_IV.color = UIColor.black
        loader_IV.autoresizingMask = [.flexibleBottomMargin, .flexibleTopMargin, .flexibleLeftMargin, .flexibleRightMargin]
        loader_IV.center = self.view.center;
        self.view.addSubview(loader_IV)
        
        return loader_IV
    }
    
    func configureRTLView(){
        let languageType = UserDefaults.standard.value(forKey: Utils.LANGUAGE_IS_RTL_KEY)
        
        if(languageType != nil){
            let languageType_str = languageType as! String
            
            if(languageType_str == Utils.DATABASE_RTL_STR){
                UIView.appearance().semanticContentAttribute = .forceRightToLeft
            }else{
                UIView.appearance().semanticContentAttribute = .forceLeftToRight
            }
        }else{
            UIView.appearance().semanticContentAttribute = .forceLeftToRight
        }
    }
    
    var className: String {
        return NSStringFromClass(self.classForCoder).components(separatedBy: ".").last!;
    }
    
    func getPubNubConfig()->String{
        if(GeneralFunctions.getValue(key: Utils.ENABLE_PUBNUB_KEY) != nil){
            return GeneralFunctions.getValue(key: Utils.ENABLE_PUBNUB_KEY) as! String
        }
        return ""
    }
    
    func closeDrawerMenu(){
        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        
        if(Configurations.isRTLMode()){
            //            self.navigationDrawerController?.setRightViewOpened(isRightViewOpened: false)
            self.navigationDrawerController?.closeRightView()
            
            //            self.navigationDrawerController?.setRightViewOpened(isRightViewOpened: true)
        }else{
            self.navigationDrawerController?.closeLeftView()
        }
    }
}
