//
//  UpdateFreqTask.swift
//  DriverApp
//
//  Created by NEW MAC on 25/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

@objc protocol OnTaskRunCalledDelegate:class
{
    func onTaskRun(currInst:UpdateFreqTask)
}

class UpdateFreqTask: NSObject {
    
    typealias OnTaskRunCalledHandler = (_ currentTask:UpdateFreqTask) -> Void
    
    var interval:Double?
    
    //    var onTaskRunCalled:OnTaskRunCalledDelegate!
    weak var onTaskRunCalled:OnTaskRunCalledDelegate?
    
    var isFirstRun = true
    var isAvoidFirstRun = false
    var isKilled = false
    
    var currInst:UpdateFreqTask!
    
    var freqTimer:Timer!
    
    var handler:OnTaskRunCalledHandler!
    
//    var currentTimeInterval:Int64!
    
    init(interval:Double) {
        self.interval = interval
        super.init()
    }
    
    func setTaskRunListener(onTaskRunCalled:OnTaskRunCalledDelegate){
        self.onTaskRunCalled = onTaskRunCalled
    }
    
    func setTaskRunHandler(handler:@escaping OnTaskRunCalledHandler){
        self.handler = handler
    }
    
    func startRepeatingTask(){
        isKilled = false
        isFirstRun = true
//        onTaskRun()
        
        DispatchQueue.main.async() {
            self.start()
        }
    }
    
    private func start(){
        if(self.freqTimer != nil){
            self.freqTimer.invalidate()
            self.freqTimer = nil
        }
        self.freqTimer =  Timer.scheduledTimer(timeInterval: self.interval!, target: self.currInst, selector: #selector(self.currInst.onTaskRun), userInfo: nil, repeats: true)
        //            self.freqTimer = Timer(fireAt: Date(), interval: 25.0, target: self.currInst, selector: #selector(self.currInst.onTaskRun), userInfo: nil, repeats: true)
        self.freqTimer.fire()
    }
    
    func onTaskRun(){
//        if(currentTimeInterval != nil){
//            let timeDifference = Utils.currentTimeMillis() - currentTimeInterval!
//            Utils.printLog(msgData: "timeDifference::\(timeDifference)")
//            if(timeDifference < Int64(Double(interval! * 1000))){
//                return
//            }else{
//                currentTimeInterval = Utils.currentTimeMillis()
//            }
//        }else{
//            currentTimeInterval = Utils.currentTimeMillis()
//        }
        
//        Utils.printLog(msgData: "Timer:Valid:\(freqTimer.isValid)::\(currentTimeInterval!)::func\(Utils.currentTimeMillis())::")
        if(self.isKilled == true){
            return
        }
        
        if(isFirstRun == true && isAvoidFirstRun == false){
            isFirstRun = false
            DispatchQueue.main.async  {
                self.callDelegate()
            }
        }else{
            self.isAvoidFirstRun = false

            if(self.isKilled != true){
                DispatchQueue.main.async  {
                    self.callDelegate()
                }
            }
            
//            DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(Double(interval!) * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
//                if(self.isKilled != true){
//                    self.callDelegate()
//                }
//            })
        }
        
    }
    
    func callDelegate(){
        if(handler != nil){
            handler(currInst)
        }
        onTaskRunCalled?.onTaskRun(currInst: currInst)
//        onTaskRun()
    }
    
    func stopRepeatingTask(){
        self.isKilled = true
        if(freqTimer != nil){
            freqTimer.invalidate()
        }
    }
    
}

