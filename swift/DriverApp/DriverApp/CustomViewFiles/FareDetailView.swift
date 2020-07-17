//
//  FareDetailView.swift
//  DriverApp
//
//  Created by NEW MAC on 26/07/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class FareDetailView: UIView, MyBtnClickDelegate, MyLabelClickDelegate {
    
    
    typealias CompletionHandler = (_ isViewRemoved:Bool, _ view:UIView, _ isMoreDetailTapped:Bool) -> Void
    
    @IBOutlet weak var cabTypeImgView: UIImageView!
    @IBOutlet weak var cabTypeNameLbl: MyLabel!
    @IBOutlet weak var capacityHLbl: MyLabel!
    @IBOutlet weak var capacityVLbl: MyLabel!
    @IBOutlet weak var fareHLbl: MyLabel!
    @IBOutlet weak var fareVLbl: MyLabel!
    @IBOutlet weak var moreDetailsLbl: MyLabel!
    @IBOutlet weak var noteLbl: MyLabel!
    @IBOutlet weak var doneBtn: MyButton!
    
    
    var view: UIView!
    var handler:CompletionHandler!
    
    override init(frame: CGRect) {
        // 1. setup any properties here
        
        // 2. call super.init(frame:)
        super.init(frame: frame)
        
        // 3. Setup view from .xib file
        xibSetup()
    }
    
    required init?(coder aDecoder: NSCoder) {
        // 1. setup any properties here
        
        // 2. call super.init(coder:)
        super.init(coder: aDecoder)
        
        // 3. Setup view from .xib file
        xibSetup()
    }
    
    func setViewHandler(handler: @escaping CompletionHandler){
        self.handler = handler
    }
    
    func xibSetup() {
        view = loadViewFromNib()
        
        // use bounds not frame or it'll be offset
        view.frame = bounds
        
        // Make the view stretch with containing view
        //        view.autoresizingMask = [UIViewAutoresizing.flexibleWidth, UIViewAutoresizing.flexibleHeight]
        // Adding custom subview on top of our view (over any custom drawing > see note below)
        addSubview(view)
        
        doneBtn.clickDelegate = self
        moreDetailsLbl.setClickDelegate(clickDelegate: self)
    }
    
    func myLableTapped(sender: MyLabel) {
        if(sender == self.moreDetailsLbl){
            
            self.view.frame = CGRect(x:0,y:0, width:0,height:0)
            self.view.isHidden = true
            self.view.removeFromSuperview()
            
            if(handler != nil){
                handler(true, view, true)
            }
        }
    }
    func myBtnTapped(sender: MyButton) {
        if(sender == self.doneBtn){
            self.view.frame = CGRect(x:0,y:0, width:0,height:0)
            self.view.isHidden = true
            self.view.removeFromSuperview()
            
            if(handler != nil){
                handler(true, view, false)
            }
        }
    }
    
    func loadViewFromNib() -> UIView {
        
        let bundle = Bundle(for: type(of: self))
        let nib = UINib(nibName: "FareDetailView", bundle: bundle)
        let view = nib.instantiate(withOwner: self, options: nil)[0] as! UIView
        
        return view
    }
    
}
