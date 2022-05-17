<?php

namespace App\Mail\Pms;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use niklasravnsborg\LaravelPdf\Pdf;

class RequestForProposalToSupplierMail extends Mailable //implements shouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $requestProposal='';
    public $supplier='';
    public $subj='';
    public $proposalType='';

    public function __construct($supplier,$requestProposal,$subj,$proposalType=null,$pdfFile=null)
    {
        $this->supplier=$supplier;
        $this->requestProposal=$requestProposal;
        $this->subj=$subj;
        $this->proposalType=$proposalType;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $supplier=$this->supplier;
        $requestProposal=$this->requestProposal;
        $proposalType=$this->proposalType;


        return $this->subject($this->subj)->view('pms.backend.mail.request-proposal-mail',compact('supplier','requestProposal','proposalType'));
    }
}
