//
//  HomeScreenContainerUV.swift
//  PassengerApp
//
//  Created by NEW MAC on 30/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class HomeScreenContainerUV: UIViewController {

    @IBOutlet weak var containerView: UIView!
    
    var mainScreenUV:MainScreenUV!
    
    
    var isPageLoad = false
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
        if(mainScreenUV != nil && mainScreenUV.isDriverAssigned != true){
            self.navigationController?.navigationBar.isHidden = true
            
            UIApplication.shared.statusBarStyle = UIStatusBarStyle.default
        }
        
        if(Configurations.isRTLMode()){
            self.navigationDrawerController?.isRightPanGestureEnabled = true
        }else{
            self.navigationDrawerController?.isLeftPanGestureEnabled = true
        }
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        self.navigationController?.navigationBar.isHidden = false
        
        UIApplication.shared.statusBarStyle = UIStatusBarStyle.lightContent
        
        if(Configurations.isRTLMode()){
            self.navigationDrawerController?.isRightPanGestureEnabled = false
        }else{
            self.navigationDrawerController?.isLeftPanGestureEnabled = false
        }
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        let navHeight = self.navigationController!.navigationBar.frame.height
        let width = ((navHeight * 350) / 119)
        let imageView = UIImageView(frame: CGRect(x: 0, y: 0, width: width, height: ((width * 119) / 350)))
        imageView.contentMode = .scaleAspectFit
        
        let image = UIImage(named: "ic_your_logo")
        imageView.image = image
        
        self.navigationItem.titleView = imageView
        
        let rightButton: UIBarButtonItem = UIBarButtonItem(image: UIImage(named: "ic_trans")!, style: UIBarButtonItemStyle.plain, target: self, action: nil)
        self.navigationItem.rightBarButtonItem = rightButton
        
        
            mainScreenUV = GeneralFunctions.instantiateViewController(pageName: "MainScreenUV") as! MainScreenUV
            mainScreenUV.view.frame = self.containerView.frame
            mainScreenUV.navItem = self.navigationItem
            
            
            self.addChildViewController(mainScreenUV)
            self.addSubview(subView: mainScreenUV.view, toView: self.containerView)
        
        
        NotificationCenter.default.addObserver(self, selector: #selector(self.releaseAllTask), name: NSNotification.Name(rawValue: Utils.releaseAllTaskObserverKey), object: nil)
        
        
    }
    
    func openMenu(){
        if(Configurations.isRTLMode()){
            //            self.navigationDrawerController?.setRightViewOpened(isRightViewOpened: false)
            self.navigationDrawerController?.toggleRightView()
            
            //            self.navigationDrawerController?.setRightViewOpened(isRightViewOpened: true)
        }else{
            self.navigationDrawerController?.toggleLeftView()
        }
    }
    
    deinit {
//        print("HomeDeinit")
    }
    
    func releaseAllTask(){
        if(mainScreenUV == nil){
            return
        }
//        print("HomeScreenReleased")
        mainScreenUV.getAddressFrmLocation?.addressFoundDelegate = nil
        mainScreenUV.getAddressFrmLocation = nil
        
        mainScreenUV.gMapView?.clear()
        mainScreenUV.gMapView?.delegate = nil
        mainScreenUV.gMapView?.stopRendering()
        mainScreenUV.gMapView?.removeFromSuperview()
        mainScreenUV.gMapView = nil
        
        mainScreenUV.releaseAllTask()
        mainScreenUV.view.removeFromSuperview()
        mainScreenUV.removeFromParentViewController()
        mainScreenUV.dismiss(animated: true, completion: nil)
        
        mainScreenUV = nil
        
        GeneralFunctions.removeObserver(obj: self)
        
        self.navigationDrawerController?.dismiss(animated: true, completion: nil)
    }
    
    
    
    
    override func viewDidAppear(_ animated: Bool) {
        if(isPageLoad == false){
             
            
            if(mainScreenUV != nil){
                mainScreenUV.view.frame = self.containerView.frame
            }
            
            isPageLoad = true
        }
    }
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    func addSubview(subView:UIView, toView parentView:UIView) {
        
        subView.frame = parentView.frame
        subView.center = CGPoint(x: parentView.bounds.midX, y: parentView.bounds.midY)
        
        parentView.addSubview(subView)
    }


}
