//
//  ProviderDetailView.swift
//  PassengerApp
//
//  Created by NEW MAC on 02/08/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class ProviderDetailView: UIView, MyBtnClickDelegate {

    typealias CompletionHandler = (_ view:UIView, _ isViewClose:Bool, _ isContinueBtnTapped:Bool) -> Void
    
    @IBOutlet weak var providerPicTopMargin: NSLayoutConstraint!
    @IBOutlet weak var closeImgViewTopMargin: NSLayoutConstraint!
    @IBOutlet weak var headerViewHeight: NSLayoutConstraint!
    @IBOutlet weak var headerView: UIView!
    @IBOutlet weak var headerImgView: UIImageView!
    @IBOutlet weak var providerImgView: UIImageView!
    @IBOutlet weak var providerNameLbl: MyLabel!
    @IBOutlet weak var ratingBar: RatingView!
    @IBOutlet weak var ratingCountLbl: MyLabel!
    @IBOutlet weak var priceLbl: MyLabel!
    @IBOutlet weak var providerDetailTxtView: UITextView!
    @IBOutlet weak var sendBtn: MyButton!
    @IBOutlet weak var closeImgView: UIImageView!
    @IBOutlet weak var scrollView: UIScrollView!
    @IBOutlet weak var scrollContentViewHeight: NSLayoutConstraint!
    @IBOutlet weak var distanceLbl: MyLabel!
    @IBOutlet weak var awayLbl: MyLabel!
    @IBOutlet weak var aboutLbl: MyLabel!
   
    var view: UIView!
    
    let generalFunc = GeneralFunctions()
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
        
//        sendBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SEND_REQUEST"))
        sendBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_NEXT_TXT"))
        
        
        sendBtn.clickDelegate = self
        
        let closeTapGue = UITapGestureRecognizer()
        closeTapGue.addTarget(self, action: #selector(self.closeView))
        
        self.closeImgView.isUserInteractionEnabled = true
        self.closeImgView.addGestureRecognizer(closeTapGue)
        
        
        headerImgView.removeFromSuperview()
        headerView.backgroundColor = UIColor.UCAColor.AppThemeColor
        
        GeneralFunctions.setImgTintColor(imgView: closeImgView, color: UIColor.UCAColor.AppThemeTxtColor)
    }
    
    
    func loadViewFromNib() -> UIView {
        
        let bundle = Bundle(for: type(of: self))
        let nib = UINib(nibName: "ProviderDetailView", bundle: bundle)
        let view = nib.instantiate(withOwner: self, options: nil)[0] as! UIView
        
        return view
    }
    
    func closeView(){
        if(self.handler != nil){
            self.handler(view,true,false)
        }
    }

    func myBtnTapped(sender: MyButton) {
        if(sender == self.sendBtn){
            if(self.handler != nil){
                self.handler(view,false,true)
            }
        }
    }
}
